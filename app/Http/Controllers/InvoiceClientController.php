<?php

namespace App\Http\Controllers;

use App\Models\InvoiceClient;
use Illuminate\Http\Request;

class InvoiceClientController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:active,inactive']
        ]);

        $rows = InvoiceClient::query()
            ->when(!empty($validated['status']), function ($query) use ($validated) {
                $query->where('status', $validated['status']);
            })
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $rows
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'in:active,inactive']
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = 'active';
        }

        $row = InvoiceClient::create($validated);

        return response()->json([
            'data' => $row
        ], 201);
    }
}
