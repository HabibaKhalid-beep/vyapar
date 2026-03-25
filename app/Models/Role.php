<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'business_id',
        'is_default',
        'is_service_staff',
    ];

    /**
     * Permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * Sync the role's permissions.
     *
     * @param  array  $permissions  Array of permission names or IDs.
     */
    public function syncPermissions(array $permissions = [])
    {
        $permissionIds = [];

        foreach ($permissions as $permission) {
            if (is_numeric($permission)) {
                $permissionIds[] = (int) $permission;
                continue;
            }

            $perm = Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);

            $permissionIds[] = $perm->id;
        }

        $this->permissions()->sync($permissionIds);
    }
}
