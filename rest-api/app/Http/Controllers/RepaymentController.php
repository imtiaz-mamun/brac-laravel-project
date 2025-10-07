<?php

namespace App\Http\Controllers;

use App\Models\Repayment;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RepaymentController extends Controller
{
    /**
     * Display a listing of repayments with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Repayment::with('loan.client');

        // Filter by loan if provided
        if ($request->filled('loan_id')) {
            $query->where('loan_id', $request->loan_id);
        }

        // Filter by payment mode if provided
        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        // Filter by payment date range
        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        $repayments = $query->orderBy('payment_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($repayments);
    }

    /**
     * Store a newly created repayment
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'amount_paid' => 'required|numeric|min:1',
            'payment_mode' => 'required|in:CASH,BANK,MOBILE',
            'reference_no' => 'nullable|string|max:255'
        ]);

        // Check if loan is active
        $loan = Loan::findOrFail($validated['loan_id']);
        if ($loan->status !== 'ACTIVE') {
            return response()->json([
                'message' => 'Cannot add repayment to inactive loan'
            ], 422);
        }

        // Check if payment amount is reasonable (not more than remaining balance + some buffer)
        $totalAmount = $loan->loan_amount * (1 + ($loan->interest_rate / 100));
        $totalRepaid = $loan->repayments()->sum('amount_paid');
        $remainingBalance = $totalAmount - $totalRepaid;

        if ($validated['amount_paid'] > $remainingBalance + ($remainingBalance * 0.1)) {
            return response()->json([
                'message' => 'Payment amount exceeds reasonable limit'
            ], 422);
        }

        DB::transaction(function () use ($validated, $loan, &$repayment) {
            $repayment = Repayment::create($validated);

            // Check if loan should be marked as closed
            $totalAmount = $loan->loan_amount * (1 + ($loan->interest_rate / 100));
            $totalRepaid = $loan->repayments()->sum('amount_paid');

            if ($totalRepaid >= $totalAmount) {
                $loan->update(['status' => 'CLOSED']);
            }
        });

        $repayment->load('loan.client');

        return response()->json([
            'message' => 'Repayment recorded successfully',
            'data' => $repayment
        ], 201);
    }

    /**
     * Display the specified repayment
     */
    public function show(Repayment $repayment): JsonResponse
    {
        $repayment->load('loan.client');

        return response()->json([
            'data' => $repayment
        ]);
    }

    /**
     * Update the specified repayment
     */
    public function update(Request $request, Repayment $repayment): JsonResponse
    {
        $validated = $request->validate([
            'payment_date' => 'sometimes|date|before_or_equal:today',
            'amount_paid' => 'sometimes|numeric|min:1',
            'payment_mode' => 'sometimes|in:CASH,BANK,MOBILE',
            'reference_no' => 'nullable|string|max:255'
        ]);

        $repayment->update($validated);
        $repayment->load('loan.client');

        return response()->json([
            'message' => 'Repayment updated successfully',
            'data' => $repayment
        ]);
    }

    /**
     * Remove the specified repayment
     */
    public function destroy(Repayment $repayment): JsonResponse
    {
        // Revert loan status if needed
        $loan = $repayment->loan;
        $repayment->delete();

        // Check if loan should be reverted to active
        $totalAmount = $loan->loan_amount * (1 + ($loan->interest_rate / 100));
        $totalRepaid = $loan->repayments()->sum('amount_paid');

        if ($totalRepaid < $totalAmount && $loan->status === 'CLOSED') {
            $loan->update(['status' => 'ACTIVE']);
        }

        return response()->json([
            'message' => 'Repayment deleted successfully'
        ]);
    }

    /**
     * Get repayments by loan
     */
    public function getByLoan(Loan $loan): JsonResponse
    {
        $repayments = $loan->repayments()
            ->orderBy('payment_date', 'desc')
            ->paginate(15);

        // Calculate loan summary
        $totalAmount = $loan->loan_amount * (1 + ($loan->interest_rate / 100));
        $totalRepaid = $loan->repayments()->sum('amount_paid');
        $remainingBalance = $totalAmount - $totalRepaid;

        return response()->json([
            'repayments' => $repayments,
            'loan_summary' => [
                'loan_amount' => $loan->loan_amount,
                'interest_rate' => $loan->interest_rate,
                'total_amount_due' => $totalAmount,
                'total_repaid' => $totalRepaid,
                'remaining_balance' => $remainingBalance,
                'status' => $loan->status
            ]
        ]);
    }
}
