<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Auth;

class CalendarEventService
{
    protected Calendar $calendarService;

    public Client $client;

    public function __construct()
    {
        if (!Auth::check()) {
            throw new \Exception('User not authenticated');
        }
        $user = Auth::user();
        $this->client = (new GoogleCalendarService())->getAuthenticatedClient($user);
        $this->calendarService = new Calendar($this->client);
    }

    public function createEvent(array $eventData): Event
    {
        $event = new Event($eventData);
        $caldarID = 'primary';
        try {
            $createdEvent = $this->calendarService->events->insert($caldarID, $event);
            return $createdEvent;
        } catch (\Exception $e) {
            throw new \Exception('Error creating event: ' . $e->getMessage());
        }
    }

    public function updateEvent(string $eventId, array $eventData): Event
    {
        $event = new Event($eventData);
        $calendarId = 'primary';
        try {
            $updatedEvent = $this->calendarService->events->update($calendarId, $eventId, $event);
            return $updatedEvent;
        } catch (\Exception $e) {
            throw new \Exception('Error updating event: ' . $e->getMessage());
        }
    }

    public function deleteEvent(string $eventId): void
    {
        $calendarId = 'primary';
        try {
            $this->calendarService->events->delete($calendarId, $eventId);
        } catch (\Exception $e) {
            throw new \Exception('Error deleting event: ' . $e->getMessage());
        }
    }
}
