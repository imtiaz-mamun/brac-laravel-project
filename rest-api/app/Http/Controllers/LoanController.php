<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Client;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LoanController extends Controller
{
    /**
     * Display a listing of loans with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Loan::with(['client', 'branch']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by branch if provided
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by client if provided
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by issue date range
        if ($request->filled('from_date')) {
            $query->whereDate('issue_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('issue_date', '<=', $request->to_date);
        }

        $loans = $query->withCount('repayments')
            ->orderBy('issue_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($loans);
    }

    /**
     * Store a newly created loan
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'branch_id' => 'required|exists:branches,id',
            'loan_amount' => 'required|numeric|min:1000|max:1000000',
            'interest_rate' => 'required|numeric|min:0|max:50',
            'issue_date' => 'required|date',
            'tenure_months' => 'required|integer|min:1|max:120'
        ]);

        $loan = Loan::create($validated);
        $loan->load(['client', 'branch']);

        return response()->json([
            'message' => 'Loan created successfully',
            'data' => $loan
        ], 201);
    }

    /**
     * Display the specified loan
     */
    public function show(Loan $loan): JsonResponse
    {
        $loan->load(['client', 'branch', 'repayments']);
        $loan->loadCount('repayments');

        return response()->json([
            'data' => $loan
        ]);
    }

    /**
     * Update the specified loan
     */
    public function update(Request $request, Loan $loan): JsonResponse
    {
        // Only allow updating if loan is not closed or defaulted
        if (in_array($loan->status, ['CLOSED', 'DEFAULTED'])) {
            return response()->json([
                'message' => 'Cannot update closed or defaulted loans'
            ], 422);
        }

        $validated = $request->validate([
            'loan_amount' => 'sometimes|numeric|min:1000|max:1000000',
            'interest_rate' => 'sometimes|numeric|min:0|max:50',
            'tenure_months' => 'sometimes|integer|min:1|max:120'
        ]);

        $loan->update($validated);
        $loan->load(['client', 'branch']);

        return response()->json([
            'message' => 'Loan updated successfully',
            'data' => $loan
        ]);
    }

    /**
     * Remove the specified loan
     */
    public function destroy(Loan $loan): JsonResponse
    {
        // Check if loan has repayments
        if ($loan->repayments()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete loan with existing repayments'
            ], 422);
        }

        $loan->delete();

        return response()->json([
            'message' => 'Loan deleted successfully'
        ]);
    }

    /**
     * Update loan status
     */
    public function updateStatus(Request $request, Loan $loan): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:ACTIVE,CLOSED,DEFAULTED'
        ]);

        $loan->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Loan status updated successfully',
            'data' => $loan
        ]);
    }

    /**
     * Get loans by client
     */
    public function getByClient(Client $client): JsonResponse
    {
        $loans = $client->loans()
            ->with(['branch', 'repayments'])
            ->withCount('repayments')
            ->orderBy('issue_date', 'desc')
            ->paginate(15);

        return response()->json($loans);
    }

    /**
     * Get loans by branch
     */
    public function getByBranch(Branch $branch): JsonResponse
    {
        $loans = $branch->loans()
            ->with(['client', 'repayments'])
            ->withCount('repayments')
            ->orderBy('issue_date', 'desc')
            ->paginate(15);

        return response()->json($loans);
    }

    /**
     * Get loan summary analytics
     */
    public function loanSummary(): JsonResponse
    {
        $summary = [
            'total_loans' => Loan::count(),
            'total_disbursed' => Loan::sum('loan_amount'),
            'loans_by_status' => Loan::selectRaw('status, COUNT(*) as count, SUM(loan_amount) as total_amount')
                ->groupBy('status')
                ->get(),
            'average_loan_amount' => Loan::avg('loan_amount'),
            'average_interest_rate' => Loan::avg('interest_rate'),
            'loans_this_month' => Loan::whereMonth('issue_date', now()->month)
                ->whereYear('issue_date', now()->year)
                ->count()
        ];

        return response()->json([
            'data' => $summary
        ]);
    }
}
