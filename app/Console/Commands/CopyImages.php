<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copiar imagens do storage para a pasta pública';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourcePath = storage_path('images');
        $destinationPath = public_path('images');

        File::makeDirectory($destinationPath, $mode = 0755, true, true);
        File::copyDirectory($sourcePath, $destinationPath);

        $this->info('Imagens copiadas com sucesso.');
    }
}
