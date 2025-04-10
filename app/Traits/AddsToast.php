<?php

namespace App\Traits;

trait AddsToast
{
    protected function addToast(string $title, string $message, string $type = 'success', bool $alwaysShow = false): void
    {
        $toast = [
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'alwaysShow' => $alwaysShow
        ];

        session()->flash('toast', $toast);
    }
}
