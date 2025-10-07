<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    /**
     * Display a listing of clients with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Client::with('branch');

        // Filter by branch if provided
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by gender if provided
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by registration date range
        if ($request->filled('from_date')) {
            $query->whereDate('registration_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('registration_date', '<=', $request->to_date);
        }

        $clients = $query->withCount('loans')
            ->orderBy('name')
            ->paginate($request->get('per_page', 15));

        return response()->json($clients);
    }

    /**
     * Store a newly created client
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:MALE,FEMALE,OTHER',
            'branch_id' => 'required|exists:branches,id',
            'registration_date' => 'required|date'
        ]);

        $client = Client::create($validated);
        $client->load('branch');

        return response()->json([
            'message' => 'Client created successfully',
            'data' => $client
        ], 201);
    }

    /**
     * Display the specified client
     */
    public function show(Client $client): JsonResponse
    {
        $client->load(['branch', 'loans.repayments']);
        $client->loadCount('loans');

        return response()->json([
            'data' => $client
        ]);
    }

    /**
     * Update the specified client
     */
    public function update(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:MALE,FEMALE,OTHER',
            'branch_id' => 'sometimes|exists:branches,id',
            'registration_date' => 'sometimes|date'
        ]);

        $client->update($validated);
        $client->load('branch');

        return response()->json([
            'message' => 'Client updated successfully',
            'data' => $client
        ]);
    }

    /**
     * Remove the specified client
     */
    public function destroy(Client $client): JsonResponse
    {
        // Check if client has loans
        if ($client->loans()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete client with existing loans'
            ], 422);
        }

        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully'
        ]);
    }

    /**
     * Get clients by branch
     */
    public function getByBranch(Branch $branch): JsonResponse
    {
        $clients = $branch->clients()
            ->withCount('loans')
            ->orderBy('name')
            ->paginate(15);

        return response()->json($clients);
    }

    /**
     * Get client statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_clients' => Client::count(),
            'clients_by_gender' => Client::selectRaw('gender, COUNT(*) as count')
                ->groupBy('gender')
                ->get(),
            'clients_by_branch' => Client::with('branch:id,name')
                ->selectRaw('branch_id, COUNT(*) as count')
                ->groupBy('branch_id')
                ->get(),
            'recent_registrations' => Client::whereDate('registration_date', '>=', now()->subDays(30))
                ->count()
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}
