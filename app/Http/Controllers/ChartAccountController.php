<?php

namespace App\Http\Controllers;

use App\Models\ChartAccount;
use Illuminate\Http\Request;

class ChartAccountController extends Controller
{
    public function index()
    {
        $rows = ChartAccount::query()
            ->with(['transactions' => function ($query) {
                $query->with('payrollRun:id,name');
            }])
            ->orderBy('code')
            ->get();

        return response()->json([
            'data' => $rows
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:finance_chart_accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:asset,liability,equity,revenue,expense'],
            'subcategory' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'string', 'in:active,inactive']
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = 'active';
        }

        $validated['total_debit'] = 0;
        $validated['total_credit'] = 0;
        $validated['balance'] = 0;

        $account = ChartAccount::create($validated);

        return response()->json([
            'data' => $account
        ], 201);
    }
}
