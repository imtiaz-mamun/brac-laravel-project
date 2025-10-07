<?php

namespace App\Console\Commands;

use App\Models\Repayment;
use App\Mail\RepaymentConfirmation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestRepaymentEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:repayment-email {repayment_id?} {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test repayment confirmation email functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $repaymentId = $this->argument('repayment_id');
        $testEmail = $this->option('email');

        if (!$repaymentId) {
            // Get the latest repayment
            $repayment = Repayment::with('loan.client', 'loan.branch')->latest()->first();

            if (!$repayment) {
                $this->error('No repayments found in the database.');
                return 1;
            }
        } else {
            $repayment = Repayment::with('loan.client', 'loan.branch')->find($repaymentId);

            if (!$repayment) {
                $this->error("Repayment with ID {$repaymentId} not found.");
                return 1;
            }
        }

        $this->info("Testing email for repayment ID: {$repayment->id}");
        $this->info("Client: {$repayment->loan->client->name}");
        $this->info("Amount: ৳{$repayment->amount_paid}");
        $this->info("Reference: {$repayment->reference_no}");

        try {
            $recipients = [];

            if ($testEmail) {
                $recipients[] = $testEmail;
                $this->info("Sending test email to: {$testEmail}");
            } elseif ($repayment->loan->client->email) {
                $recipients[] = $repayment->loan->client->email;
                $this->info("Sending email to client: {$repayment->loan->client->email}");
            } else {
                $this->warn("Client has no email address. Use --email option to specify test email.");
                return 1;
            }

            // CC: Admin email
            $ccRecipients = [];
            if (config('mail.cc_mail')) {
                $ccRecipients[] = config('mail.cc_mail');
                $this->info("CC: " . config('mail.cc_mail'));
            }

            Mail::to($recipients)
                ->cc($ccRecipients)
                ->send(new RepaymentConfirmation($repayment));

            $this->info('✅ Email sent successfully!');

            $this->newLine();
            $this->info('Email details:');
            $this->line("- To: " . implode(', ', $recipients));
            if (!empty($ccRecipients)) {
                $this->line("- CC: " . implode(', ', $ccRecipients));
            }
            $this->line("- Subject: Loan Repayment Confirmation - Reference: {$repayment->reference_no}");

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}