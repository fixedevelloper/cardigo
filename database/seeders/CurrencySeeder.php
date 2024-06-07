<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('currencies')->insert(array(
            0 =>
                array(
                    'id' => '1',
                    'name' => 'XAF',
                    'code' => 'FCFA',
                    'symbol' => 'FCFA',
                    'exchange' => 1.0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ),
            1 =>
                array(
                    'id' => '2',
                    'name' => 'XOF',
                    'code' => 'FCFA',
                    'symbol' => 'FCFA',
                    'exchange' => 1.0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ),
            2 =>
                array(
                    'id' => '3',
                    'name' => 'Dollar(america)',
                    'code' => 'dollar',
                    'symbol' => '$',
                    'exchange' => 600.0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ),
            3 =>
                array(
                    'id' => '4',
                    'name' => 'Euro',
                    'code' => 'euro',
                    'symbol' => '£',
                    'exchange' => 650.0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ),));
    }
}
