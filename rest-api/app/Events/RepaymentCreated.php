<?php

namespace App\Events;

use App\Models\Repayment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RepaymentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $repayment;

    /**
     * Create a new event instance.
     */
    public function __construct(Repayment $repayment)
    {
        $this->repayment = $repayment;
    }
}