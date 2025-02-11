<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VccCredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('vcc_credentials')->insert([
            'username' => 'macatawalegends',
            'password' => 'Mac2021!',
            'is_active' => true,
        ]);
    }
}
