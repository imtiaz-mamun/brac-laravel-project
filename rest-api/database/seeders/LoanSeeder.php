<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\Client;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $clients = Client::with('branch')->get();

        // Create loans for about 80% of clients
        $clientsWithLoans = $clients->random(160);

        foreach ($clientsWithLoans as $client) {
            // Some clients might have multiple loans
            $loanCount = $faker->randomElement([1, 1, 1, 2, 2, 3]);

            for ($i = 0; $i < $loanCount; $i++) {
                $loanAmount = $faker->randomElement([
                    10000,
                    15000,
                    20000,
                    25000,
                    30000,
                    40000,
                    50000,
                    75000,
                    100000,
                    150000,
                    200000,
                    250000
                ]);

                $interestRate = $faker->randomFloat(2, 8, 18); // 8% to 18%
                $tenureMonths = $faker->randomElement([6, 12, 18, 24, 36, 48]);

                $issueDate = $faker->dateTimeBetween('-18 months', '-1 month');

                // Determine status based on issue date and randomness
                $monthsElapsed = now()->diffInMonths($issueDate);
                $status = 'ACTIVE';

                if ($monthsElapsed >= $tenureMonths) {
                    $status = $faker->randomElement(['CLOSED', 'CLOSED', 'CLOSED', 'DEFAULTED']);
                } elseif ($monthsElapsed > ($tenureMonths * 0.8)) {
                    $status = $faker->randomElement(['ACTIVE', 'ACTIVE', 'CLOSED']);
                }

                Loan::create([
                    'client_id' => $client->id,
                    'branch_id' => $client->branch_id,
                    'loan_amount' => $loanAmount,
                    'interest_rate' => $interestRate,
                    'issue_date' => $issueDate->format('Y-m-d'),
                    'tenure_months' => $tenureMonths,
                    'status' => $status
                ]);
            }
        }
    }
}
