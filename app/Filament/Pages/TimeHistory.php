<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use Carbon\Carbon;
use Filament\Pages\Page;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class TimeHistory extends Page
{

    protected static ?string $modelLabel = 'Histórico';

    protected static ?string $pluralLabel = 'Histórico de Apontamentos';

    protected static ?string $navigationLabel = 'Histórico de Apontamentos';

    protected static ?string $navigationIcon = 'heroicon-s-clock';

    protected static ?string $title = 'Histórico de Apontamentos';

    protected static string $view = 'filament.pages.time-history.index';

    protected static ?int $navigationSort = 1;

    public $token = null;

    public $date = null;

    public $entries = [];

    public $spent = 0;

    public function mount()
    {

        $user = Auth::user();

        $employee = Employee::where('user_id', $user->id)->first();

        $this->token = $employee->redmine_token;

        $this->date = date('Y-m-d');

        $client = new Client();

        $url = 'https://www.hostedredmine.com/time_entries.json';

        $params = [
            'spent_on' => $this->date,
            'key' => $this->token,
            'user_id' => 'me'
        ];

        $response = $client->request('GET', $url, [
            'query' => $params
        ]);

        $data = json_decode($response->getBody(), true);

        $entries = [];

        foreach($data['time_entries'] as $entry) {

            if(date('Y-m-d', strtotime($entry['spent_on'])) == $this->date) {

                $url = "https://www.hostedredmine.com/issues/".$entry['issue']['id'].".json?key=".$this->token;

                $response = $client->request('GET', $url, []);

                $data = json_decode($response->getBody(), true);

                $entry['issue_name'] = $data['issue']['subject'];

                $entries[] = $entry;

                $this->spent += $entry['hours'];

            }

        }

        $this->entries = $entries;

    }

    public function search()
    {

        $this->spent = 0;

        $client = new Client();

        $url = 'https://www.hostedredmine.com/time_entries.json';

        $params = [
            'spent_on' => $this->date,
            'key' => $this->token,
            'user_id' => 'me'
        ];

        $response = $client->request('GET', $url, [
            'query' => $params
        ]);

        $data = json_decode($response->getBody(), true);

        // Próximo dia de busca

        $date = Carbon::parse($this->date);

        $nextDay = $date->addDay();

        $newDate = $nextDay->format('Y-m-d');

        $newParams = [
            'spent_on' => $newDate,
            'key' => $this->token,
            'user_id' => 'me'
        ];

        $newResponse = $client->request('GET', $url, [
            'query' => $newParams
        ]);

        $newData = json_decode($newResponse->getBody(), true);

        ///////////////////////

        $timeEntries = array_merge($data['time_entries'], $newData['time_entries']);

        $entries = [];

        foreach($timeEntries as $entry) {

            if(date('Y-m-d', strtotime($entry['spent_on']) - (3 * 3600)) == $this->date) {

                $url = "https://www.hostedredmine.com/issues/".$entry['issue']['id'].".json?key=".$this->token;

                $response = $client->request('GET', $url, []);

                $data = json_decode($response->getBody(), true);

                $entry['issue_name'] = $data['issue']['subject'];

                $entries[] = $entry;

                $this->spent += $entry['hours'];

            }

        }

        $this->entries = $entries;

    }

}
