<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Store a newly created transaction in storage and update the client's credit balance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:achat,acompte', // Adjust 'other' to match your transaction types
            'montant' => 'required|numeric',
            'designation' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'clientId' => 'required|integer|exists:clients,id',
        ]);

        DB::beginTransaction();

        try {
            $client = Client::findOrFail($request->input('clientId'));

            // Determine the new credit balance
            $newGredit = $client->gredit;
            if ($request->input('type') === 'achat') {
                $newGredit += $request->input('montant');
            } else {
                $newGredit -= $request->input('montant');
            }

       

            // Create the transaction
            $transaction = Transaction::create([
                'type' => $request->input('type'),
                'montant' => (float) $request->input('montant'),
                'designation' => $request->input('designation'),
                'date' => $request->input('date') ? new \DateTime($request->input('date')) : null,
                'clientId' => $request->input('clientId'),
                'currentSoldeCredit' => $newGredit, // Use the new credit balance here
            ]);

            // Update the client's credit balance and other details
            $client->update([
                'gredit' => $newGredit,
                'date' => $request->input('date') ? new \DateTime($request->input('date')) : $client->date,
                'designation' => $request->input('designation'),
            ]);

            DB::commit();

            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create transaction'], 500);
        }
    }

    public function index($clientId)
    {
        try {
            // Fetch transactions for the specific clientId
            $transactions = Transaction::where('clientId', $clientId)
                ->orderBy('date', 'desc') // Optional: Order transactions by date, most recent first
                ->get();

            return response()->json($transactions, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch transactions'], 500);
        }
    }
}
