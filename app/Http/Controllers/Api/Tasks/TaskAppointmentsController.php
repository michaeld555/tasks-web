<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Actions\System\GetEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class TaskAppointmentsController extends Controller
{

    public function __invoke(Request $request, $taskId)
    {

        $employee = GetEmployee::search($request->bearerToken());

        if(is_null($employee)) {
            $this->sendError('Usuário não encontrado', 404);
        }

        $task = $this->getTaskInformation($employee->redmine_token, $taskId);

        $appointments = $this->getTaskAppointments($employee->redmine_token, $taskId);

        $data = [
            'appointments' => $this->formatAppointments($task['issues'][0], $appointments),
        ];

        return $this->sendResponse($data);

    }

    public function getTaskInformation(string $token, string $taskId) {

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $url = env('REDMINE_ENDPOINT') . "/issues.json?issue_id=$taskId&key=$token";

        $request = new GuzzleRequest('GET', $url, $headers);

        try {

            $response = $client->sendAsync($request)->wait();

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;

        } catch (Exception $e) {

            $this->sendError(400, 'Ocorreu um erro na busca da tarefa, tente novamente mais tarde.');

        }

    }

    public function getTaskAppointments(string $token, string $taskId) {

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $url = env('REDMINE_ENDPOINT') . "/time_entries.json?issue_id=$taskId&key=$token&limit=100";

        $request = new GuzzleRequest('GET', $url, $headers);

        try {

            $response = $client->sendAsync($request)->wait();

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;

        } catch (Exception $e) {

            $this->sendError(400, 'Ocorreu um erro na busca dos apontamentos, tente novamente mais tarde.');

        }

    }

    public function formatAppointments(array $task, array $appointments)
    {

        $response = [];

        foreach ($appointments['time_entries'] as $appointment) {

            $response[] = [
                'issue_id' => $task['id'],
                'appointment_id' => $appointment['id'],
                'issue_title' => $task['subject'],
                'project_name' => $task['project']['name'],
                'user_id' => $appointment['user']['id'],
                'user_name' => $appointment['user']['name'],
                'type_appointment_issue' => $appointment['activity']['name'],
                'spent_time' => $appointment['hours'] ?? 0,
                'comment' => $appointment['comments'],
                'appointment_date' => $appointment['spent_on'],
                'created_at' => $appointment['created_on'],
                'updated_at' => $appointment['updated_on'],
            ];

        }

        return $response;

    }

}
