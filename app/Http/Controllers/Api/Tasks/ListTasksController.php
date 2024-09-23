<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Actions\System\GetEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class ListTasksController extends Controller
{

    public function __invoke(Request $request, $project)
    {

        $token = $request->bearerToken();

        $employee = GetEmployee::search($token);

        if(is_null($employee)) {
            $this->sendError('Usuário não encontrado', 404);
        }

        $params = [
            'identificadorProjeto.equals' => $project,
            'page' => 0,
            'size' => 1000,
            'tipoIntegracaoId.equals' => 1,
            'atribuidoPara.contains' => $employee->full_name,
            'sort' => 'prioridadeSituacao,asc',
        ];

        $tasks = $this->getProjectTasks($token, $params);

        $response = [
            'tasks' => $this->formatTasks($tasks),
        ];

        return $this->sendResponse($response);

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

    public function formatTasks(array $tasks)
    {

        $response = [];

        foreach($tasks as $task) {

            $response[] = [
                'issue_id' => $task['id'],
                'parent_id' => $task['idOrigem'],
                'priority' => $task['prioridade'],
                'title' => $task['titulo'],
                'description' => $task['descricao'],
                'type_issue' => $task['tipo'],
                'version' => $task['versao'],
                'assigned_to' => $task['atribuidoPara'],
                'estimated_time' => $task['horasEstimadas'] ?? 0,
                'spent_time' => $task['horasGastas'] ?? 0,
                'percentage' => $task['percentualTermino'] ?? 0,
                'issue_status' => $task['situacao'],
                'appointment_status' => $task['statusApontamento'],
                'sequence' => $task['sequencia'],
                'started_at' => $task['dataInicio'],
                'created_at' => $task['dataCriacao'],
                'closing_date' => $task['dataVencimento'],
            ];

        }

        return $response;

    }

}
