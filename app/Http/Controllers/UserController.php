<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('department', 'branches')
            ->whereNotIn('role', ['admin', 'super_admin'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($user) {
                $user->hashed_id = Hashids::encode($user->id);
                return $user;
            });

        $roleMap = [
            'hr_manager' => 'Manager',
            'cr_manager' => 'Manager',
            'finance_manager' => 'Manager',
            'supply_chain_manager' => 'Manager',
            'inventory_manager' => 'Inventory Manager',
            'procurement_manager' => 'Procurement Manager',
            'operations_manager' => 'Manager',
            'project_manager' => 'Manager',
            'site_supervisor' => 'Supervisor',
            'foreman' => 'Foreman',
            'supplier_coordinator' => 'Coordinator',
            'accountant' => 'Accountant',
        ];

        return response()->json([
            'ok' => true,
            'users' => $users->map(function($u) use ($roleMap) {
                return [
                    'id' => $u->hashed_id,
                    'account_id' => $u->account_id,
                    'name' => $u->full_name,
                    'email' => $u->email,
                    'role' => $roleMap[$u->role] ?? ucfirst(str_replace('_', ' ', (string) $u->role)),
                    'department' => $u->department->name ?? null,
                    'branches' => $u->branches->pluck('name')->toArray(),
                    'phone' => $u->phone,
                    'address' => $u->address ?? null,
                    'gender' => $u->gender ?? null,
                    'date_of_birth' => $u->date_of_birth ?? null,
                    'employee_type' => $u->employee_type ?? null,
                    'payroll_group' => $u->payroll_group ?? null,
                    'pay_type' => $u->pay_type ?? null,
                    'work_schedule' => $u->work_schedule ?? null,
                    'created_at' => $u->created_at,
                    'status' => $u->status,
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Store user method called', ['request' => $request->all()]);

        DB::beginTransaction();

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('accounts', 'public');
            Log::info('Image uploaded', ['path' => $path]);
        } else {
            Log::info('No image uploaded');
        }

        do {
            $randomNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $account_id = 'U-' . $randomNumber;
        } while (User::where('account_id', $account_id)->exists());

        try {
            $user = User::create([
                'last_name' => ucfirst(strtolower($request->last_name)),
                'first_name' => ucfirst(strtolower($request->first_name)),
                'middle_name' => $request->middle_name ? ucfirst(strtolower($request->middle_name)) : null,
                'account_id' => $account_id,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => bcrypt('eoshma123'),
                'role' => $request->role,
                'image' => $path,
            ]);
            Log::info('User created', ['user_id' => $user->id]);

            $departments = [
                'hrm' => 'HR',
                'crm' => 'Client Relations',
                'frm' => 'Finance',
                'scm' => 'Supply Chain',
                'mrp' => 'Procurement',
                'om'  => 'Operations',
            ];

            $departmentName = $departments[$request->department] ?? $request->department;

            Department::create([
                'users_id' => $user->id,
                'name' => $departmentName,
            ]);

            Log::info('Department created', ['department' => $request->department]);

            if ($request->role) {
                $user->assignRole($request->role);
                Log::info('Role assigned', ['role' => $request->role]);
            }

            if ($request->permissions) {
                $permissions = collect(json_decode($request->permissions, true))
                    ->map(fn ($p) => "{$p['page']}.{$p['type']}")
                    ->filter(fn ($perm) => Permission::where('name', $perm)->exists());

                $user->syncPermissions($permissions);
                Log::info('Permissions synced', ['permissions' => $permissions]);
            }

            DB::commit();
            Log::info('Transaction committed');

            return response()->json(['message' => 'User created']);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Failed'], 500);
        }
    }

    // public function checkUserEmail(Request $request)
    // {
    //     $exists = User::where('email', $request->query('email'))->exists();

    //     return response()->json(['exists' => $exists]);
    // }

    // public function editUserName($id, $name)
    // {   
    //     $decoded = Hashids::decode($id);
    //     $id = $decoded[0];

    //     $user = User::find($id);

    //     $user->name = $name;
    //     $user->save();

    //     return response()->json([
    //         'message' => 'Name edited successfully!'
    //     ], 201);
    // }

    public function editUserPassword($id, $password)
    {
        $user = User::find($id);

        $user->password = bcrypt($password);
        $user->save();

        return response()->json([
            'message' => 'Password changed!'
        ], 201);
    }

    public function resetPassword($id)
    {   
        $decoded = Hashids::decode($id);
        $id = $decoded[0];

        $user = User::find($id);

        $user->password = bcrypt('eoshma123');
        $user->save();

        return response()->json([
            'message'=>'Password changed!'
        ]);
    }

}
