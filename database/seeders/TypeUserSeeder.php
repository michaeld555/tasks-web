<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeUserSeeder extends Seeder
{

    /**
     * Cadastro dos tipos de usuários do sistema
     */
    public function run(): void
    {

        DB::table('type_users')->insert([
            [
                'id' => 1,
                'type' => 'Administrador'
            ],
            [
                'id' => 2,
                'type' => 'Colaborador'
            ],
        ]);

    }

}
