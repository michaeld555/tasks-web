<?php

namespace App\Services\Layout;

use Swis\Filament\Backgrounds\Contracts\ProvidesImages;
use Swis\Filament\Backgrounds\Image;

class LoginImage implements ProvidesImages
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getImage(): Image
    {
        return new Image(
            'url("'.asset('images/layout/background.png').'")',
            'Gerenciador de Tarefas'
        );
    }
}
