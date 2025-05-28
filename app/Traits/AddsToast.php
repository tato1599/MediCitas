<?php

namespace App\Traits;

trait AddsToast
{
    protected function addToast(string $title, string $message, string $type = 'success', bool $redirect = false): void
    {
        $toast = [
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
        ];

        if ($redirect) {
            session()->flash('toast', $toast);
        } else {
            $this->js("Toast.fire({
                icon: '{$type}',
                title: '{$title}',
                text: '{$message}'
            })");
        }
    }
}
