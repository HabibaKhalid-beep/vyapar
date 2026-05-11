<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartyGroup;
use App\Models\Party;
use Illuminate\Support\Facades\DB;

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

    public function update(Request $request, PartyGroup $partyGroup)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $newName = trim($data['name']);
        $targetGroup = null;

        DB::transaction(function () use ($partyGroup, $newName, &$targetGroup) {
            $existingGroup = PartyGroup::where('name', $newName)
                ->where('id', '!=', $partyGroup->id)
                ->first();

            Party::where('party_group', $partyGroup->name)->update([
                'party_group' => $newName,
            ]);

            if ($existingGroup) {
                $partyGroup->delete();
                $targetGroup = $existingGroup;
                return;
            }

            $partyGroup->update([
                'name' => $newName,
            ]);

            $targetGroup = $partyGroup->fresh();
        });

        return response()->json([
            'success' => true,
            'partyGroup' => $targetGroup,
        ]);
    }

    public function destroy(PartyGroup $partyGroup)
    {
        DB::transaction(function () use ($partyGroup) {
            Party::where('party_group', $partyGroup->name)->update([
                'party_group' => null,
            ]);

            $partyGroup->delete();
        });

        return response()->json([
            'success' => true,
        ]);
    }
}
