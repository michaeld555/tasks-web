<?php

namespace App\Livewire;

use App\Models\User;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

class UserProfile extends MyProfileComponent
{

    protected string $view = "filament.pages.profile.index";

    public array $data;

    public function mount(): void
    {
        $user = User::find((Auth::user())->id);

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
        ];

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('name')
                    ->label('Nome')
                    ->disabled(),

                TextInput::make('email')
                        ->label('Email')
                        ->disabled(),

                TextInput::make('username')
                        ->label('Usuário do Gerenciador')
                        ->disabled(),

            ])
            ->statePath('data');
    }

}

