<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Retrieve permission names derived from user roles.
     */
    public function getAllPermissions(): array
    {
        // Super-admin shortcut: user ID 1 has all permissions by default.
        // This addresses the 'admin click does nothing / blank page' case when no roles are linked.
        if ($this->id === 1) {
            return Permission::pluck('name')->toArray();
        }

        $rolePermissions = $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn ($role) => $role->permissions->pluck('name'))
            ->unique()
            ->values()
            ->toArray();

        // If no role-based permissions are assigned, give a default safe set for admin users only.
        if (empty($rolePermissions) && $this->id === 1) {
            return Permission::pluck('name')->toArray();
        }

        return $rolePermissions;
    }

    public function hasPermission(string $permission): bool
    {
        // Super-admin user id=1 should always have access regardless of DB permissions.
        if ($this->id === 1) {
            return true;
        }

        return in_array($permission, $this->getAllPermissions());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
