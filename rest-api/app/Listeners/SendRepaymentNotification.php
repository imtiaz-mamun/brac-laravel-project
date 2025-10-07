<?php

namespace App\Listeners;

use App\Events\RepaymentCreated;
use App\Mail\RepaymentConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRepaymentNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RepaymentCreated $event): void
    {
        try {
            $repayment = $event->repayment;
            $client = $repayment->loan->client;

            // Primary recipient: client (if they have email)
            $recipients = [];
            if ($client->email) {
                $recipients[] = $client->email;
            }

            // CC: Admin email
            $ccRecipients = [];
            if (config('mail.cc_mail')) {
                $ccRecipients[] = config('mail.cc_mail');
            }

            // Send email if we have at least one recipient
            if (!empty($recipients) || !empty($ccRecipients)) {
                // If no primary recipients but we have CC recipients, use CC as primary
                if (empty($recipients) && !empty($ccRecipients)) {
                    $recipients = $ccRecipients;
                    $ccRecipients = [];
                }

                Mail::to($recipients)
                    ->cc($ccRecipients)
                    ->send(new RepaymentConfirmation($repayment));

                Log::info('Repayment confirmation email sent', [
                    'repayment_id' => $repayment->id,
                    'client_id' => $client->id,
                    'amount' => $repayment->amount_paid,
                    'reference_no' => $repayment->reference_no,
                    'recipients' => $recipients,
                    'cc' => $ccRecipients
                ]);
            } else {
                Log::warning('No email recipients found for repayment notification', [
                    'repayment_id' => $repayment->id,
                    'client_id' => $client->id,
                    'client_email' => $client->email
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send repayment confirmation email', [
                'repayment_id' => $event->repayment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(RepaymentCreated $event, \Throwable $exception): void
    {
        Log::error('Repayment notification job failed', [
            'repayment_id' => $event->repayment->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}