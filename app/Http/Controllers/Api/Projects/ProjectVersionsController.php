<?php

namespace App\Http\Controllers\Api\Projects;

use App\Actions\System\GetEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class ProjectVersionsController extends Controller
{

    public function __invoke(Request $request, $project)
    {

        $employee = GetEmployee::search($request->bearerToken());

        if(is_null($employee)) {
            $this->sendError('Usuário não encontrado', 404);
        }

        $versions = $this->getProjectVersions($employee->redmine_token, $project);

        $data = [
            'versions' => $this->formatVersions($versions),
        ];

        return $this->sendResponse($data);

    }

    public function getProjectVersions(string $token, string $project) {

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $url = env('REDMINE_ENDPOINT') . "/projects/$project/versions.json?key=$token";

        $request = new GuzzleRequest('GET', $url, $headers);

        try {

            $response = $client->sendAsync($request)->wait();

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;

        } catch (Exception $e) {

            $this->sendError(400, 'Ocorreu um erro na busca das versões, tente novamente mais tarde.');

        }

    }

    public function formatVersions(array $versions)
    {

        $response = [];

        foreach ($versions['versions'] as $version) {

            $response[] = [
                'version_id' => $version['id'],
                'version_name' => $version['name'],
                'description' => $version['description'],
                'status' => $version['status'],
                'created_at' => $version['created_on'],
                'updated_at' => $version['updated_on'],
                'closed_on' => $version['due_date'],
            ];

        }

        return $response;

    }

}
