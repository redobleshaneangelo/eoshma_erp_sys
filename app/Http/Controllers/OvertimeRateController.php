<?php

namespace App\Http\Controllers;

use App\Models\OvertimeRate;
use Illuminate\Http\Request;

class OvertimeRateController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => OvertimeRate::query()
                ->orderBy('label')
                ->get()
        ]);
    }

    public function update(Request $request, OvertimeRate $overtimeRate)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'multiplier' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean']
        ]);

        $overtimeRate->update($validated);

        return response()->json([
            'data' => $overtimeRate
        ]);
    }
}
