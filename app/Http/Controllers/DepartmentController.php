<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index()
    {
        return Department::with('user')->get();
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'users_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
        ]);

        $department = Department::create($validated);

        return response()->json($department, 201);
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        return $department->load('user');
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'users_id' => 'sometimes|exists:users,id',
            'name' => 'sometimes|string|max:255',
        ]);

        $department->update($validated);

        return response()->json($department);
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json(['message' => 'Department deleted successfully']);
    }
}
