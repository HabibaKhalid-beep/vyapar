<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartyGroup;

class PartyGroupController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $partyGroup = PartyGroup::firstOrCreate([
            'name' => trim($data['name']),
        ]);

        return response()->json([
            'success' => true,
            'partyGroup' => $partyGroup,
        ]);
    }
}
