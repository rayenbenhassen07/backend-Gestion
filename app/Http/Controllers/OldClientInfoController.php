<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OldClientInfo;

class OldClientInfoController extends Controller
{
    /**
     * Retrieve a specific old client info by ID.
     */
    public function show($oldClientId)
    {
        try {
            $client = OldClientInfo::find($oldClientId);

            if ($client) {
                return response()->json($client, 200);
            } else {
                return response()->json(['error' => 'Client not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch client'], 500);
        }
    }
}
