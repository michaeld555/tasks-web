<?php

namespace App\Http\Controllers\Api\Projects;

use App\Actions\System\GetEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class ProjectMembersController extends Controller
{

    public function __invoke(Request $request, $project)
    {

        $employee = GetEmployee::search($request->bearerToken());

        if(is_null($employee)) {
            $this->sendError('Usuário não encontrado', 404);
        }

        $members = $this->getProjectMembers($employee->redmine_token, $project);

        $data = [
            'members' => $this->formatMembers($members),
        ];

        return $this->sendResponse($data);

    }

    public function getProjectMembers(string $token, string $project) {

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $url = env('REDMINE_ENDPOINT') . "/projects/$project/memberships.json?key=$token";

        $request = new GuzzleRequest('GET', $url, $headers);

        try {

            $response = $client->sendAsync($request)->wait();

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;

        } catch (Exception $e) {

            $this->sendError(400, 'Ocorreu um erro na busca dos membros, tente novamente mais tarde.');

        }

    }

    public function formatMembers(array $members)
    {

        $response = [];

        foreach ($members['memberships'] as $member) {

            $response[] = [
                'member_logo' =>'https://ui-avatars.com/api/?name='.urlencode($member['user']['name']).'&background=0D8ABC&color=fff',
                'member_name' => $member['user']['name'],
                'redmine_id' => $member['id'],
            ];

        }

        return $response;

    }

}
