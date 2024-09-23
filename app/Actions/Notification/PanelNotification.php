<?php

namespace App\Actions\Notification;

use Filament\Notifications\Notification;

class PanelNotification
{

    /**
     * Creates a notification in the Filament admin panel
     *
     * @param integer $type
     * @param string $title
     * @param string $message
     * @return void
     */
    public static function create(int $type = 1, String $title = '', String $message = ''): void
    {

        if($type == 1){

            Notification::make()
            ->success()
            ->title($title)
            ->body($message)
            ->send();

        }

        if($type == 2){

            Notification::make()
            ->info()
            ->title($title)
            ->body($message)
            ->send();

        }

        if($type == 3){

            Notification::make()
            ->warning()
            ->title($title)
            ->body($message)
            ->send();

        }

        if($type == 4){

            Notification::make()
            ->danger()
            ->title($title)
            ->body($message)
            ->send();

        }

    }
}
