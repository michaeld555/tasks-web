<?php

namespace App\Livewire;

use App\Actions\Notification\PanelNotification;
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
            'manager_user' => $user->manager_user,
            'manager_password' => $user->manager_password,
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

                TextInput::make('manager_user')
                        ->label('Usuário do Gerenciador')
                        ->required(),

                TextInput::make('manager_password')
                        ->label('Senha do Gerenciador')
                        ->password()
                        ->revealable()
                        ->required(),

            ])
            ->statePath('data');
    }

    public function submit(): void
    {

        $data = $this->form->getState();

        $user = User::find((Auth::user())->id);
        $user->manager_user = $data['manager_user'];
        $user->manager_password = $data['manager_password'];
        $user->save();

        PanelNotification::create(1, 'Informações do perfil atualizadas com sucesso');

    }

}

