<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Actions\System\TokenDecoder;
use App\Models\Employee;
use App\Models\EmployeeSetting;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Hash;



class LoginController extends Controller
{

    public function __invoke(LoginRequest $request)
    {

        $fields = $request->all();

        $response = $this->authenticateUser($fields['username'], $fields['password']);

        $account = $this->getAccountInfo($response['id_token']);

        $user = $this->verifyAndUpdateUser($account, $response['id_token']);

        $data = [
            'token' => $response['id_token'],
        ];

        return $this->sendResponse($data);

    }

    public function authenticateUser($username, $password, $remember = false) {

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

            $this->sendError(401, 'Usuário ou senha incorretos, tente novamente.');

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

            $this->sendError(400, 'Ocorreu um erro na autenticação, tente novamente mais tarde.');

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
                'profile_photo' =>'https://ui-avatars.com/api/?name='.$account['firstName'].'+'.$account['lastName'].'&background=0D8ABC&color=fff',
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
                'profile_photo' =>'https://ui-avatars.com/api/?name='.$account['firstName'].'+'.$account['lastName'].'&background=0D8ABC&color=fff',
            ]);

        }

        return $user;

    }

}
