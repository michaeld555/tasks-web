<?php

namespace App\Http\Controllers\Api\Projects;

use App\Actions\System\GetEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class ProjectController extends Controller
{

    public function __invoke(Request $request, $project)
    {

        $token = $request->bearerToken();

        $employee = GetEmployee::search($token);

        if(is_null($employee)) {
            $this->sendError('Usuário não encontrado', 404);
        }

        $identifiers = [$project];

        $params = [
            'ativo.equals' => true,
            'identificador.in' => $identifiers,
            'sort' => 'nome,asc',
            'size' => 1,
        ];

        $information = $this->getProject($token, $params);

        $params = [
            'identificadorProjeto.equals' => $project,
            'page' => 0,
            'size' => 1000,
            'tipoIntegracaoId.equals' => 1,
            'atribuidoPara.contains' => $employee->full_name,
            'sort' => 'prioridadeSituacao,asc',
        ];

        $tasks = $this->getProjectTasks($token, $params);

        $response = $this->formatProject($information, $tasks);

        return $this->sendResponse($response);

    }

    public function getProject($token, array $params = []) {

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

            $this->sendError(400, 'Ocorreu um erro na busca do projeto, tente novamente mais tarde.');

        }

    }

    public function getProjectTasks($token, array $params = []) {

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];

        $url = env('API_ENDPOINT') . '/tarefas';

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $request = new GuzzleRequest('GET', $url, $headers);

        try {

            $response = $client->sendAsync($request)->wait();

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;

        } catch (Exception $e) {

            $this->sendError(400, 'Ocorreu um erro na busca das tarefas do projeto, tente novamente mais tarde.');

        }

    }

    public function formatProject(array $information, array $tasks)
    {

        $timeSpent = 0;

        $project = $information[0];

        $closedTasks = array_filter($tasks, function ($task) {
            return $task['situacao'] === "Closed";
        });

        $runningTasks = array_filter($tasks, function ($task) {
            return $task['statusApontamento'] === "EM_ANDAMENTO";
        });

        foreach($tasks as $task) {
            $time = floatval($task['horasGastas']) ?? 0;
            $timeSpent += $time;
        }

        $response = [
            'project_id' => $project['id'],
            'project_name' => $project['nome'],
            'identifier' => $project['identificador'],
            'project_logo' =>'https://ui-avatars.com/api/?name='.urlencode($project['nome']).'&background=0D8ABC&color=fff',
            'status' => $project['ativo'],
            'version' => $project['sprintAtual'],
            'members_count' => count($project['usuarios']),
            'closed_issues_count' => count($closedTasks),
            'running_issues_count' => count($runningTasks),
            'open_issues_count' => count($tasks) - count($closedTasks),
            'total_time_spent' => $timeSpent,
            'issues_count' => count($tasks),
            'running_issues' => (count($runningTasks) > 0) ? true : false,
            'created_at' => $project['criadoEm'],
        ];

        return $response;

    }

}
