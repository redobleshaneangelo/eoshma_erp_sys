<?php

namespace App\Http\Controllers;

use App\Models\PayrollGroup;
use Illuminate\Http\Request;

class PayrollGroupController extends Controller
{
    public function index()
    {
        $groups = PayrollGroup::query()
            ->withCount('employees')
            ->orderBy('name')
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'salaryType' => $group->salary_type,
                    'members' => $group->employees_count
                ];
            });

        return response()->json(['data' => $groups]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'salary_type' => ['required', 'in:fixed,hour,day']
        ]);

        $group = PayrollGroup::create([
            'name' => $validated['name'],
            'salary_type' => $validated['salary_type']
        ]);

        return response()->json([
            'data' => [
                'id' => $group->id,
                'name' => $group->name,
                'salaryType' => $group->salary_type,
                'members' => 0
            ]
        ], 201);
    }
}
