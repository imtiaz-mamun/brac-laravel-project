<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BranchController extends Controller
{
    /**
     * Display a listing of branches with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Branch::query();

        // Filter by region if provided
        if ($request->filled('region')) {
            $query->where('region', 'like', '%' . $request->region . '%');
        }

        // Filter by district if provided
        if ($request->filled('district')) {
            $query->where('district', 'like', '%' . $request->district . '%');
        }

        $branches = $query->withCount(['clients', 'loans'])
            ->orderBy('name')
            ->paginate($request->get('per_page', 15));

        return response()->json($branches);
    }

    /**
     * Store a newly created branch
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:branches',
            'district' => 'required|string|max:255',
            'region' => 'required|string|max:255'
        ]);

        $branch = Branch::create($validated);

        return response()->json([
            'message' => 'Branch created successfully',
            'data' => $branch
        ], 201);
    }

    /**
     * Display the specified branch with related data
     */
    public function show(Branch $branch): JsonResponse
    {
        $branch->loadCount(['clients', 'loans']);

        return response()->json([
            'data' => $branch
        ]);
    }

    /**
     * Update the specified branch
     */
    public function update(Request $request, Branch $branch): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:branches,name,' . $branch->id,
            'district' => 'sometimes|string|max:255',
            'region' => 'sometimes|string|max:255'
        ]);

        $branch->update($validated);

        return response()->json([
            'message' => 'Branch updated successfully',
            'data' => $branch
        ]);
    }

    /**
     * Remove the specified branch
     */
    public function destroy(Branch $branch): JsonResponse
    {
        // Check if branch has clients or loans
        if ($branch->clients()->count() > 0 || $branch->loans()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete branch with existing clients or loans'
            ], 422);
        }

        $branch->delete();

        return response()->json([
            'message' => 'Branch deleted successfully'
        ]);
    }

    /**
     * Get branch performance analytics
     */
    public function performance(): JsonResponse
    {
        $branches = Branch::withCount(['clients', 'loans'])
            ->with([
                'loans' => function ($query) {
                    $query->selectRaw('branch_id, SUM(loan_amount) as total_disbursed, COUNT(*) as loan_count')
                        ->groupBy('branch_id');
                }
            ])
            ->get()
            ->map(function ($branch) {
                $totalDisbursed = $branch->loans->sum('loan_amount');
                $activeLoans = $branch->loans()->where('status', 'ACTIVE')->count();

                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'district' => $branch->district,
                    'region' => $branch->region,
                    'clients_count' => $branch->clients_count,
                    'loans_count' => $branch->loans_count,
                    'total_disbursed' => $totalDisbursed,
                    'active_loans' => $activeLoans
                ];
            });

        return response()->json([
            'data' => $branches
        ]);
    }
}
