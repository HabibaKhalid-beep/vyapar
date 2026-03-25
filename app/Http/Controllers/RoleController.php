<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('name')->get();

        $enabled_modules = session('business.enabled_modules', []);
        $pos_settings = ! empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
        $common_settings = session('business.common_settings', []);
        $selling_price_groups = [];

        // Module permissions are used in the included module permissions partial.
        $module_permissions = [];

        return view('dashboard.roles.index', compact(
            'roles',
            'enabled_modules',
            'pos_settings',
            'common_settings',
            'selling_price_groups',
            'module_permissions'
        ));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $enabled_modules = session('business.enabled_modules', []);
        $pos_settings = ! empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
        $common_settings = session('business.common_settings', []);
        $selling_price_groups = [];

        // Module permissions are used in the included module permissions partial.
        $module_permissions = [];

        return view('dashboard.roles.create', compact(
            'enabled_modules',
            'pos_settings',
            'common_settings',
            'selling_price_groups',
            'module_permissions'
        ));
    }

    /**
     * Show the form for editing an existing role.
     */
    public function edit(Role $role)
    {
        $enabled_modules = session('business.enabled_modules', []);
        $pos_settings = ! empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
        $common_settings = session('business.common_settings', []);
        $selling_price_groups = [];
        $module_permissions = [];
        $role_permissions = $role->permissions()->pluck('name')->toArray();

        return view('dashboard.roles.create', compact(
            'role',
            'role_permissions',
            'enabled_modules',
            'pos_settings',
            'common_settings',
            'selling_price_groups',
            'module_permissions'
        ));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
            'spg_permissions' => ['array'],
            'spg_permissions.*' => ['string'],
            'radio_option' => ['array'],
            'radio_option.*' => ['string'],
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->input('name'),
                'guard_name' => 'web',
                'business_id' => null,
                'is_default' => false,
                'is_service_staff' => (bool) $request->input('is_service_staff', false),
            ]);

            $permissions = $request->input('permissions', []);
            $permissions = array_merge($permissions, $request->input('spg_permissions', []));
            $permissions = array_merge($permissions, array_values($request->input('radio_option', [])));
            $permissions = array_filter(array_unique($permissions));

            $role->syncPermissions($permissions);
        });

        return redirect()->route('roles.index')->with('success', 'Role added successfully.');
    }

    /**
     * Update an existing role.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
            'spg_permissions' => ['array'],
            'spg_permissions.*' => ['string'],
            'radio_option' => ['array'],
            'radio_option.*' => ['string'],
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'name' => $request->input('name'),
                'is_service_staff' => (bool) $request->input('is_service_staff', false),
            ]);

            $permissions = $request->input('permissions', []);
            $permissions = array_merge($permissions, $request->input('spg_permissions', []));
            $permissions = array_merge($permissions, array_values($request->input('radio_option', [])));
            $permissions = array_filter(array_unique($permissions));

            $role->syncPermissions($permissions);
        });

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
