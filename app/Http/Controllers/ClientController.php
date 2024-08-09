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
        'name' => 'required|string|max:255|unique:clients,name',
        'num' => 'nullable|string|max:255|unique:clients,num',
        'credit' => 'nullable|numeric',
        'designation' => 'nullable|string|max:255',
    ], [
        'name.required' => 'Le nom est obligatoire.',
        'name.unique' => 'Le nom doit être unique.',
        'credit.numeric' => 'Le crédit doit être un nombre.',
        'designation.max' => 'La désignation ne peut pas dépasser 255 caractères.',
    ]);

    DB::beginTransaction();

    try {
        // Set credit to 0 if it is null
        $credit = $request->input('credit') ?? 0;

        // Create a new client
        $client = Client::create([
            'name' => $request->input('name'),
            'num' => $request->input('num'),
            'gredit' => $credit,
            'designation' => $request->input('designation'),
        ]);

        // Store a transaction only if credit is not null
        if ($credit !== 0) {
            Transaction::create([
                'type' => 'achat',
                'montant' => $credit,
                'designation' => $request->input('designation'),
                'clientId' => $client->id,
                'currentSoldeCredit' => $client->gredit,
            ]);
        }

        DB::commit();

        return response()->json($client->load('transactions'), 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Échec de la création du client et de la transaction.'], 500);
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
