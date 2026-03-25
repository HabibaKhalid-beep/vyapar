<?php

require_once 'bootstrap/app.php';

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check admin user
$adminUser = User::find(1);
echo "=== ADMIN USER (ID 1) ===\n";
echo "Name: " . ($adminUser?->name ?? 'NOT FOUND') . "\n";
echo "ID: " . ($adminUser?->id ?? 'NOT FOUND') . "\n";

if ($adminUser) {
    echo "\nRoles assigned: " . count($adminUser->roles) . "\n";
    foreach ($adminUser->roles as $role) {
        echo "  - " . $role->name . " (permissions: " . count($role->permissions) . ")\n";
    }

    echo "\nAll Permissions (from getAllPermissions()):\n";
    $perms = $adminUser->getAllPermissions();
    echo "Count: " . count($perms) . "\n";
    if (count($perms) > 0 && count($perms) <= 20) {
        foreach ($perms as $perm) {
            echo "  - " . $perm . "\n";
        }
    } else if (count($perms) > 20) {
        echo "  (showing first 20 of " . count($perms) . ")\n";
        foreach (array_slice($perms, 0, 20) as $perm) {
            echo "  - " . $perm . "\n";
        }
    }
}

echo "\n=== DATABASE PERMISSIONS ===\n";
$allPerms = Permission::all();
echo "Total permissions in DB: " . count($allPerms) . "\n";
if (count($allPerms) > 0 && count($allPerms) <= 20) {
    foreach ($allPerms as $perm) {
        echo "  - " . $perm->name . "\n";
    }
} else if (count($allPerms) > 20) {
    echo "  (showing first 20 of " . count($allPerms) . ")\n";
    foreach ($allPerms->take(20) as $perm) {
        echo "  - " . $perm->name . "\n";
    }
}
?>
