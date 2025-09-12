<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   public function run()
    {
        // Solo ejecutar el seeder si estás en entorno local
        if (app()->environment('local')) {
            $this->call(UserSeeder::class);
        }
    }


}
