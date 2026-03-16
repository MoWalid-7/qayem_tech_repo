<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Plan::query()->delete();

        \App\Models\Plan::create([
            'name' => 'Startup',
            'min_employees' => 1,
            'max_employees' => 10,
            'price_per_employee' => 15,
            'stripe_price_id' => 'price_1T9orQIU4J4Q8mCGcD9JWrG5', // Valid Stripe Price ID
        ]);

        \App\Models\Plan::create([
            'name' => 'Small',
            'min_employees' => 11,
            'max_employees' => 50,
            'price_per_employee' => 12,
            'stripe_price_id' => 'price_1T9orRIU4J4Q8mCGMQnmkq9e', // Valid Stripe Price ID
        ]);

        \App\Models\Plan::create([
            'name' => 'Medium',
            'min_employees' => 51,
            'max_employees' => 500,
            'price_per_employee' => 10,
            'stripe_price_id' => 'price_1T9orRIU4J4Q8mCGOul2B3zP', // Valid Stripe Price ID
        ]);

        \App\Models\Plan::create([
            'name' => 'Enterprise',
            'min_employees' => 501,
            'max_employees' => 10000,
            'price_per_employee' => 8,
            'stripe_price_id' => 'price_1T9orRIU4J4Q8mCGOul2B3zP', // Fallback to Medium if no Enterprise price
        ]);
    }
}
