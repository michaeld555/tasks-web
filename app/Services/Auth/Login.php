<?php

namespace App\Services\Auth;

use App\Actions\Notification\PanelNotification;
use App\Actions\System\TokenDecoder;
use App\Models\Employee;
use App\Models\EmployeeSetting;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Auth\Login as LoginPage;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class Login extends LoginPage
{

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        $username = $data['email'];

        $user = User::where('username', $username)->first();

        if(is_null($user) || $user->type_user_id != 1) {

            $response = $this->authenticateUser($data['email'], $data['password'], $data['remember']);

            $account = $this->getAccountInfo($response['id_token']);

            $user = $this->verifyAndUpdateUser($account, $response['id_token']);

            $credentials = [
                'username' => $user->username,
                'password' => $user->username,
            ];

            if (! Filament::auth()->attempt($credentials, $data['remember'] ?? false)) {
                $this->throwFailureValidationException();
            }

        } else {

            $credentials = [
                'username' => $data['email'],
                'password' => $data['password'],
            ];

            if (! Filament::auth()->attempt($credentials, $data['remember'] ?? false)) {
                $this->throwFailureValidationException();
            }

        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Usuário de acesso')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    public function authenticateUser($username, $password, $remember) {

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $body = json_encode([
            'username' => $username,
            'password' => $password,
            'rememberMe' => $remember
        ]);

        $request = new Request('POST', env('API_ENDPOINT').'/authenticate', $headers, $body);

        try {

            $response = $client->sendAsync($request)->wait();

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;

        } catch (Exception $e) {

            $this->throwFailureValidationException();

        }

    }

    public function getAccountInfo($token) {

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];

        $request = new Request('GET', env('API_ENDPOINT').'/account', $headers);

        try {

            $response = $client->sendAsync($request)->wait();

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;

        } catch (Exception $e) {

            PanelNotification::create(4, 'Erro na autenticação', 'Ocorreu um erro na autenticação, tente novamente mais tarde.');

            $this->throwFailureAccountException();

        }

    }

    public function verifyAndUpdateUser(array $account, string $token): User
    {

        $user = User::where('username', $account['login'])->first();

        $authToken = TokenDecoder::JWT($token);

        $expireToken = Carbon::createFromTimestamp($authToken['exp'])->format('Y-m-d H:i:s');

        if(is_null($user)){

            $user = User::create([
                'type_user_id' => 2,
                'name' => $account['firstName'].' '.$account['lastName'],
                'email' => $account['email'],
                'username' => $account['login'],
                'password' => Hash::make($account['login']),
            ]);

            $employee = Employee::create([
                'full_name' => $account['firstName'].' '.$account['lastName'],
                'email' => $account['email'],
                'jwt_token' => $token,
                'redmine_token' => $account['redmineToken'],
                'token_expire' => $expireToken,
                'user_id' => $user->id,
                'redmine_id' => $account['idRedmine'],
            ]);

            $settings = EmployeeSetting::create([
                'employee_id' => $employee->id,
                'working_hours' => 8,
                'working_days' => 5,
                'break_time' => '20:00',
            ]);

        } else {

            $employee = Employee::where('user_id', $user->id)->first();

            $user->update([
                'name' => $account['firstName'].' '.$account['lastName'],
                'email' => $account['email'],
                'username' => $account['login'],
            ]);

            $employee->update([
                'full_name' => $account['firstName'].' '.$account['lastName'],
                'email' => $account['email'],
                'jwt_token' => $token,
                'redmine_token' => $account['redmineToken'],
                'token_expire' => $expireToken,
                'redmine_id' => $account['idRedmine'],
            ]);

        }

        return $user;

    }

    protected function throwFailureAccountException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => 'Erro na autenticação',
        ]);
    }

}
