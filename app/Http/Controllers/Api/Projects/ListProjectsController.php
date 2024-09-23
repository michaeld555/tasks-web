<?php

namespace App\Http\Controllers\Api\Projects;

use App\Actions\System\GetEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;


class ListProjectsController extends Controller
{

    public function __invoke(Request $request)
    {

        $token = $request->bearerToken();

        $employee = GetEmployee::search($token);

        if(is_null($employee)) {
            $this->sendError('Usuário não encontrado', 404);
        }

        $account = $this->getAccountInfo($token);

        $identifiers = implode(',', array_keys($account['projetos']));

        $params = [
            'ativo.equals' => true,
            'identificador.in' => $identifiers,
            'sort' => 'nome,asc',
            'size' => 100,
        ];

        $projects = $this->getProjectsList($token, $params);

        $data = [
            'projects' => $this->formatProjects($projects),
        ];

        return $this->sendResponse($data);


    }

    public function getAccountInfo($token) {

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];

        $request = new GuzzleRequest('GET', env('API_ENDPOINT').'/account', $headers);

        try {

            $response = $client->sendAsync($request)->wait();

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;

        } catch (Exception $e) {

            $this->sendError(400, 'Ocorreu um erro na busca de projetos, tente novamente mais tarde.');

        }

    }

    public function getProjectsList($token, array $params = []) {

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];

        $url = env('API_ENDPOINT') . '/projetos';

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $request = new GuzzleRequest('GET', $url, $headers);

        try {

            $response = $client->sendAsync($request)->wait();

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;

        } catch (Exception $e) {

            $this->sendError(400, 'Ocorreu um erro na busca de projetos, tente novamente mais tarde.');

        }

    }

    public function formatProjects(array $projects)
    {

        $response = [];

        foreach ($projects as $project) {

            $response[] = [
                'project_name' => $project['nome'],
                'project_id' => $project['id'],
                'identifier' => $project['identificador'],
                'status' => $project['ativo'],
                'members_count' => count($project['usuarios']),
                'total_time_spent' => $project['tempoTotalGasto'] ?? 0,
                'version' => $project['sprintAtual'],
                'created_at' => $project['criadoEm'],
            ];

        }

        return $response;

    }

}
