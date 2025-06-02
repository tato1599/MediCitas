<?php

namespace App\Services;


use App\Models\User;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Oauth2;
use Illuminate\Support\Facades\Auth;

class GoogleCalendarService
{
    protected Client $client;

    protected Calendar $service;

    public function __construct($redirectUri = null)
    {
        $this->client = new Client();
        $this->client->setAuthConfig(base_path('client_credentials.json'));
        $this->client->addScope(Calendar::CALENDAR);
        $this->client->addScope(Oauth2::USERINFO_EMAIL);
        $this->client->addScope(Oauth2::USERINFO_PROFILE);
        $this->client->setRedirectUri($redirectUri);
        // dd($this->client);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function connect(string $code): User
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        dd($token, $code);
        $this->client->setAccessToken($token);
        // dd($token);

        $oauth2 = new Oauth2($this->client);
        // dd($oauth2);

        $userInfo = $oauth2->userinfo->get();

        if (!Auth::check()) {
            throw new \Exception('User not authenticated');
        }

        $user = Auth::user();

        $existingUser = User::where('google_id', $userInfo->id)->first();
        if ($existingUser && $existingUser->id !== $user->id) {
            throw new \Exception('Google account is already linked to another user.');
        }

        $user->google_id = $userInfo->id;
        $this->updateUserTokens($user, $token);
        return $user;
    }

    protected function updateUserTokens(User $user, array $token)
    {
        $user->google_access_token = $token['access_token'];
        $user->google_token_expires_at = Carbon::now()->addSeconds($token['expires_in']);

        // El refresh token solo se envía la primera vez que el usuario autoriza
        if (isset($token['refresh_token'])) {
            $user->google_refresh_token = $token['refresh_token'];
        }

        $user->save();
    }

    public function getAuthenticatedClient(User $user): Client
    {
        $this->client->setAccessToken([
            'access_token' => $user->google_access_token,
            'refresh_token' => $user->google_refresh_token,
            'expires_in' => $user->google_token_expires_at->getTimestamp() - time(),
        ]);

        // Si el token de acceso ha expirado, lo refrescamos
        if ($this->client->isAccessTokenExpired()) {
            $new_token = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            $this->updateUserTokens($user, $new_token);

            // Re-establecemos el token en el cliente
            $this->client->setAccessToken($new_token);
        }

        return $this->client;
    }
}
