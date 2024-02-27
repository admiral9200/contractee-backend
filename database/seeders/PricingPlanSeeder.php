<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("pricing_plans")->insert([
            'mode' => 'primary',
            'amount' => 0,
            'currency' => 'USD'
        ]);

        DB::table("pricing_plans")->insert([
            'mode' => 'premium',
            'amount' => 18,
            'currency' => 'USD'
        ]);

        DB::table("pricing_plans")->insert([
            'mode' => 'deluxe',
            'amount' => 38,
            'currency' => 'USD'
        ]);
    }
}
