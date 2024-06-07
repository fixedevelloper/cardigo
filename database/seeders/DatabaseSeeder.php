<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Helpers\Helper;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        User::factory()->create([
            'first_name' => 'Administrator',
            'email' => 'test@admin.com',
            'phone' => '237675066919',
            'user_type' => 0,
            'password' => bcrypt("123456789"),
        ]);
        $this->call(CurrencySeeder::class);
        $this->call(CountrySeeder::class);
    }
}
