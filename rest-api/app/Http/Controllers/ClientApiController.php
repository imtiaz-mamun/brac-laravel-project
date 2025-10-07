<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Repayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClientApiController extends Controller
{
    public function __construct()
    {
        // Middleware is applied at route level
    }

    /**
     * Get active loans for authenticated client
     */
    public function loans(Request $request)
    {
        $client = auth('api')->user();

        // Cache key for client's loans
        $cacheKey = "client_{$client->id}_active_loans";

        $activeLoans = Cache::remember($cacheKey, 300, function () use ($client) {
            return $client->loans()
                ->where('status', 'ACTIVE')
                ->with(['branch', 'repayments'])
                ->orderBy('issue_date', 'desc')
                ->get()
                ->map(function ($loan) {
                    return [
                        'id' => $loan->id,
                        'loan_amount' => $loan->loan_amount,
                        'interest_rate' => $loan->interest_rate,
                        'issue_date' => $loan->issue_date,
                        'tenure_months' => $loan->tenure_months,
                        'status' => $loan->status,
                        'total_repaid' => $loan->repayments->sum('amount_paid'),
                        'remaining_balance' => $loan->loan_amount - $loan->repayments->sum('amount_paid'),
                        'monthly_installment' => $this->calculateMonthlyInstallment(
                            $loan->loan_amount,
                            $loan->interest_rate,
                            $loan->tenure_months
                        ),
                        'branch' => $loan->branch->only(['id', 'name', 'district']),
                        'repayments_count' => $loan->repayments->count(),
                        'last_payment_date' => $loan->repayments->max('payment_date'),
                        'created_at' => $loan->created_at,
                    ];
                });
        });

        return response()->json([
            'message' => 'Active loans retrieved successfully',
            'data' => $activeLoans,
            'summary' => [
                'total_active_loans' => $activeLoans->count(),
                'total_outstanding' => $activeLoans->sum('remaining_balance'),
                'total_disbursed' => $activeLoans->sum('loan_amount'),
            ]
        ]);
    }

    /**
     * Get loan repayment history and analytics for authenticated client
     */
    public function loanRepaymentHistory(Request $request)
    {
        $client = auth('api')->user();

        // Get filter parameters
        $loanId = $request->get('loan_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $perPage = min($request->get('per_page', 15), 100);

        // Cache key for repayment analytics
        $analyticsCacheKey = "client_{$client->id}_repayment_analytics";

        // Build query
        $repaymentsQuery = Repayment::whereHas('loan', function ($query) use ($client) {
            $query->where('client_id', $client->id);
        })->with(['loan.branch']);

        // Apply filters
        if ($loanId) {
            $repaymentsQuery->where('loan_id', $loanId);
            $analyticsCacheKey .= "_loan_{$loanId}";
        }

        if ($startDate) {
            $repaymentsQuery->where('payment_date', '>=', $startDate);
            $analyticsCacheKey .= "_from_{$startDate}";
        }

        if ($endDate) {
            $repaymentsQuery->where('payment_date', '<=', $endDate);
            $analyticsCacheKey .= "_to_{$endDate}";
        }

        // Get paginated repayments
        $repayments = $repaymentsQuery
            ->orderBy('payment_date', 'desc')
            ->paginate($perPage);

        // Get analytics data (cached)
        $analytics = Cache::remember($analyticsCacheKey, 600, function () use ($client, $loanId, $startDate, $endDate) {
            $analyticsQuery = Repayment::whereHas('loan', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            });

            if ($loanId) {
                $analyticsQuery->where('loan_id', $loanId);
            }
            if ($startDate) {
                $analyticsQuery->where('payment_date', '>=', $startDate);
            }
            if ($endDate) {
                $analyticsQuery->where('payment_date', '<=', $endDate);
            }

            $repaymentData = $analyticsQuery->get();

            return [
                'total_payments' => $repaymentData->count(),
                'total_amount_paid' => $repaymentData->sum('amount_paid'),
                'average_payment_amount' => $repaymentData->avg('amount_paid'),
                'payment_modes' => $repaymentData->groupBy('payment_mode')->map(function ($group, $mode) use ($repaymentData) {
                    return [
                        'mode' => $mode,
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount_paid'),
                        'percentage' => $repaymentData->count() > 0 ? round(($group->count() / $repaymentData->count()) * 100, 2) : 0
                    ];
                })->values(),
                'monthly_payment_trends' => $repaymentData->groupBy(function ($item) {
                    return $item->payment_date->format('Y-m');
                })->map(function ($group, $month) {
                    return [
                        'month' => $month,
                        'payments_count' => $group->count(),
                        'total_amount' => $group->sum('amount_paid'),
                    ];
                })->values(),
                'loans_with_payments' => $repaymentData->groupBy('loan_id')->count(),
            ];
        });

        // Transform repayment data
        $repaymentData = collect($repayments->items())->map(function ($repayment) {
            return [
                'id' => $repayment->id,
                'loan_id' => $repayment->loan_id,
                'payment_date' => $repayment->payment_date,
                'amount_paid' => $repayment->amount_paid,
                'payment_mode' => $repayment->payment_mode,
                'reference_no' => $repayment->reference_no,
                'loan' => [
                    'id' => $repayment->loan->id,
                    'loan_amount' => $repayment->loan->loan_amount,
                    'status' => $repayment->loan->status,
                    'branch' => $repayment->loan->branch->only(['id', 'name', 'district'])
                ],
                'created_at' => $repayment->created_at,
            ];
        });

        return response()->json([
            'message' => 'Loan repayment history retrieved successfully',
            'data' => $repaymentData,
            'pagination' => [
                'current_page' => $repayments->currentPage(),
                'per_page' => $repayments->perPage(),
                'total' => $repayments->total(),
                'last_page' => $repayments->lastPage(),
                'from' => $repayments->firstItem(),
                'to' => $repayments->lastItem(),
            ],
            'analytics' => $analytics,
            'filters_applied' => [
                'loan_id' => $loanId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ]);
    }

    /**
     * Calculate monthly installment for a loan
     */
    private function calculateMonthlyInstallment($principal, $annualRate, $months)
    {
        if ($annualRate == 0) {
            return $principal / $months;
        }

        $monthlyRate = $annualRate / 100 / 12;
        $installment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $months)) /
            (pow(1 + $monthlyRate, $months) - 1);

        return round($installment, 2);
    }
}
