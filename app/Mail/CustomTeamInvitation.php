<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Laravel\Jetstream\TeamInvitation;

class CustomTeamInvitation extends Mailable
{
    use SerializesModels;

    public TeamInvitation $invitation;

    public function __construct(TeamInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        $url = url('/invitacion/' . $this->invitation->id);

        return $this->markdown('emails.team-invitation')
            ->subject('Invitación a un equipo')
            ->with([
                'team' => $this->invitation->team->name,
                'url' => $url,
            ]);
    }
}
