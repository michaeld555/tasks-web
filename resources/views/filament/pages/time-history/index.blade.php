<x-filament-panels::page>

    <div>

        <div wire:loading>
            <div class="fixed inset-0 flex items-center justify-center z-[100] opacity-80 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                <div role="status" class="flex items-center">
                    <svg aria-hidden="true" class="w-10 h-10 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                    <span class="text-blue-900 font-bold text-lg">Carregando...</span>
                </div>
            </div>
        </div>

        <div class="flex justify-center p-10">

            <div class="m-2 w-4/6 max-w-4xl">

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg">

                    <h2 class="text-stone-700 text-xl font-bold">Filtros de Busca</h2>

                    <p class="mt-1 text-sm">Use os filtros para encontrar o que precisa nos apontamentos</p>

                    <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-1 lg:grid-cols-1">

                        <div class="flex flex-col">
                            <label for="date" class="text-stone-600 text-sm font-medium">Data do Apontamento</label>
                            <input type="date" wire:model="date" value="{{ date('Y-m-d') }}" class="mt-2 block w-full rounded-md border border-gray-200 px-2 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                        </div>

                    </div>

                    <div class="mt-6 grid w-full grid-cols-2 justify-end space-x-4 md:flex">
                        <button wire:click="search" class="active:scale-95 rounded-lg bg-blue-600 px-8 py-2 font-medium text-white outline-none focus:ring hover:opacity-90">Buscar</button>
                    </div>

                </div>

                <div class="mx-auto bg-white my-10 flex w-full rounded-xl border px-4 py-4 text-center">
                    <div class="flex space-x-2 w-full">
                        <div class="flex flex-col items-center w-full rounded-xl bg-gray-100 px-4 py-2">
                            <p class="text-sm font-medium text-gray-500">Apontamentos</p>
                            <p class="text-3xl font-medium text-gray-600"> {{ count($entries) }} </p>
                        </div>
                        <div class="flex flex-col items-center w-full rounded-xl bg-gray-100 px-4 py-2">
                            <p class="text-sm font-medium text-gray-500">Tarefas</p>
                            <p class="text-3xl font-medium text-gray-600"> {{ count(array_unique(array_column(array_column($entries, 'issue'), 'id'))) }} </p>
                        </div>
                        <div class="flex flex-col items-center w-full rounded-xl bg-gray-100 px-4 py-2">
                            <p class="text-sm font-medium text-gray-500">Horas Gastas</p>
                            <p class="text-3xl font-medium text-gray-600"> {{ number_format($spent, 2, '.', '') }}</p>
                        </div>
                    </div>
                </div>


                @foreach($entries as $entry)

                    <div class="w-full mt-10">
                        <div class="rounded-lg border bg-white px-4 pt-8 pb-10 shadow-lg">

                            <h1 class="my-1 text-center text-xl font-bold leading-8 text-gray-900">{{ $entry['project']['name'] }}</h1>

                            <h3 class="font-lg mb-2 text-semibold text-center leading-6 text-gray-600"> #{{ $entry['issue']['id'] }} - {{ $entry['issue_name'] }} </h3>

                            <p class="text-center text-sm leading-6 text-gray-500 hover:text-gray-600">{{ $entry['comments'] }}</p>

                            <ul class="mt-3 divide-y rounded bg-gray-100 py-2 px-3 text-gray-600 shadow-sm hover:text-gray-700 hover:shadow">
                                <li class="flex items-center py-3 text-sm">
                                    <span>Tipo</span>
                                    <span class="ml-auto">
                                        <span class="rounded-full bg-green-200 py-1 px-2 text-xs font-medium text-green-700">
                                            {{ $entry['activity']['name'] }}
                                        </span>
                                    </span>
                                </li>
                                <li class="flex items-center py-3 text-sm">
                                    <span>Tempo gasto</span>
                                    <span class="ml-auto"> {{ ($h = floor($entry['hours'])) ? $h . ($h === 1 ? " hora" : " horas") . (($m = round(($entry['hours'] - $h) * 60)) ? " e $m" . ($m === 1 ? " minuto" : " minutos") : "") : round($entry['hours'] * 60) . " minutos" }} </span>
                                </li>
                                <li class="flex items-center py-3 text-sm">
                                    <span>Inicio</span>
                                    <span class="ml-auto"> {{ date('d/m/Y à\s H:i', strtotime($entry['created_on']) - ($entry['hours'] * 3600)) }} </span>
                                </li>
                                <li class="flex items-center py-3 text-sm">
                                    <span>Termino</span>
                                    <span class="ml-auto"> {{ date('d/m/Y à\s H:i', strtotime($entry['created_on'])) }} </span>
                                </li>
                            </ul>

                        </div>
                    </div>

                @endforeach


            </div>

        </div>
    </div>

</x-filament-panels::page>
