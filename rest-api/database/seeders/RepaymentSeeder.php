<?php

namespace Database\Seeders;

use App\Models\Repayment;
use App\Models\Loan;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class RepaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $loans = Loan::all();

        foreach ($loans as $loan) {
            $totalAmount = $loan->loan_amount * (1 + ($loan->interest_rate / 100));
            $monthlyPayment = $totalAmount / $loan->tenure_months;

            $issueDate = Carbon::parse($loan->issue_date);
            $currentDate = now();

            // Calculate how many months have passed
            $monthsElapsed = $issueDate->diffInMonths($currentDate);
            $monthsElapsed = min($monthsElapsed, $loan->tenure_months);

            // Create repayments based on loan status and elapsed time
            if ($loan->status === 'CLOSED') {
                // Closed loans should have full repayments
                $paymentsToMake = $loan->tenure_months;
            } elseif ($loan->status === 'DEFAULTED') {
                // Defaulted loans have partial payments
                $paymentsToMake = $faker->numberBetween(1, max(1, intval($monthsElapsed * 0.6)));
            } else {
                // Active loans have payments up to current date (with some variation)
                $paymentsToMake = $faker->numberBetween(
                    max(0, $monthsElapsed - 2),
                    min($monthsElapsed + 1, $loan->tenure_months)
                );
            }

            $totalPaid = 0;

            for ($i = 0; $i < $paymentsToMake; $i++) {
                $paymentDate = $issueDate->copy()->addMonths($i + 1);

                // Don't create future payments
                if ($paymentDate->isFuture()) {
                    break;
                }

                // Vary payment amounts slightly
                $paymentAmount = $monthlyPayment * $faker->randomFloat(2, 0.8, 1.2);

                // For the last payment of closed loans, pay exactly remaining amount
                if ($loan->status === 'CLOSED' && $i === $paymentsToMake - 1) {
                    $paymentAmount = $totalAmount - $totalPaid;
                }

                Repayment::create([
                    'loan_id' => $loan->id,
                    'payment_date' => $paymentDate->format('Y-m-d'),
                    'amount_paid' => round($paymentAmount, 2),
                    'payment_mode' => $faker->randomElement(['CASH', 'BANK', 'MOBILE']),
                    'reference_no' => $faker->optional(0.7)->numerify('TXN-########')
                ]);

                $totalPaid += $paymentAmount;

                // Break if we've paid the full amount
                if ($totalPaid >= $totalAmount) {
                    break;
                }
            }
        }
    }
}
