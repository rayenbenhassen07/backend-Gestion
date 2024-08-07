<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Store a new client and create a transaction of type "achat".
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'num' => 'nullable|string|max:255|unique:clients,num',
            'credit' => 'required|numeric',
            'designation' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Create a new client
            $client = Client::create([
                'name' => $request->input('name'),
                'num' => $request->input('num'),
                'gredit' => $request->input('credit'),
                'designation' => $request->input('designation'),
            ]);

            // Create a transaction for the client
            $transaction = Transaction::create([
                'type' => 'achat',
                'montant' => $request->input('credit'),
                'designation' => $request->input('designation'),
                'clientId' => $client->id,
                'currentSoldeCredit' => $client->gredit,
            ]);

            DB::commit();

            return response()->json($client->load('transactions'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create client and transaction'], 500);
        }
    }

    /**
     * Fetch clients with their transactions.
     */
    public function index()
    {
        try {
            $clients = Client::with('transactions')->get();
            return response()->json($clients, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch clients'], 500);
        }
    }

    /**
     * Display the specified client by ID.
     */
    public function show($clientId)
    {
        try {
            $client = Client::with('transactions')->find($clientId);

            if ($client) {
                return response()->json($client, 200);
            } else {
                return response()->json(['error' => 'Client not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch client'], 500);
        }
    }

    /**
     * Remove the specified client and its transactions from storage.
     */
    public function destroy($clientId)
    {
        DB::beginTransaction();

        try {
            // Delete all transactions related to the client
            Transaction::where('clientId', $clientId)->delete();

            // Delete the client
            $client = Client::find($clientId);
            if ($client) {
                $client->delete();
            }

            DB::commit();

            return response()->json(['message' => 'Client and related transactions deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete client and related transactions'], 500);
        }
    }
}
