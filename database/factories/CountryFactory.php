<?php


namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory  extends Factory
{

    public function definition()
    {
        $facker=fake();
        return [
            'name' => $facker->unique()->country,
            'iso' => $facker->unique()->countryISOAlpha3,
            'iso3' => $facker->unique()->countryISOAlpha3,
            'numcode' => $facker->randomDigit,
            'phonecode' => $facker->randomDigit,
            'status' => 1
        ];
    }
}
