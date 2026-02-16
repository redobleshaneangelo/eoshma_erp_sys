<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        $query = LeaveType::query()->orderBy('name');

        if ($search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $paginator = $query->paginate($perPage);

        $rows = collect($paginator->items())
            ->map(fn (LeaveType $item) => $this->transform($item))
            ->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:leave_types,name'],
            'days_per_year' => ['required', 'integer', 'min:1', 'max:365'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $leaveType = LeaveType::create([
            'name' => trim((string) $validated['name']),
            'days_per_year' => (int) $validated['days_per_year'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return response()->json(['data' => $this->transform($leaveType)], 201);
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:leave_types,name,' . $leaveType->id],
            'days_per_year' => ['required', 'integer', 'min:1', 'max:365'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $leaveType->update([
            'name' => trim((string) $validated['name']),
            'days_per_year' => (int) $validated['days_per_year'],
            'is_active' => (bool) ($validated['is_active'] ?? $leaveType->is_active),
        ]);

        return response()->json(['data' => $this->transform($leaveType->fresh())]);
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();

        return response()->json(['status' => 'ok']);
    }

    private function transform(LeaveType $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'daysPerYear' => $item->days_per_year,
            'isActive' => (bool) $item->is_active,
            'createdAt' => optional($item->created_at)?->toDateTimeString(),
            'updatedAt' => optional($item->updated_at)?->toDateTimeString(),
        ];
    }
}