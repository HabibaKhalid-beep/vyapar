<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'handler_name' => 'nullable|string|max:255',
            'handler_phone' => 'nullable|string|max:50',
            'responsible_user_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $warehouse = Warehouse::create($data)->load('responsibleUser:id,name,email');

        return response()->json([
            'success' => true,
            'warehouse' => $warehouse,
        ]);
    }
}
