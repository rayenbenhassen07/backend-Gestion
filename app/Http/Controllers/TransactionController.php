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
        'type' => 'required|string|in:achat,acompte',
        'montant' => 'required|numeric',
        'designation' => 'nullable|string|max:255',
        'date' => 'required|date',
        'clientId' => 'required|integer|exists:clients,id',
    ]);

    DB::beginTransaction();

    try {
        $client = Client::findOrFail($request->input('clientId'));
        $transactionDate = new \DateTime($request->input('date'));

        // Find the last transaction before the new transaction date
        $previousTransaction = Transaction::where('clientId', $client->id)
            ->where('date', '<=', $transactionDate)
            ->orderBy('date', 'desc')
            ->first();

        // Set the new transaction's starting credit balance
        $newGredit = $previousTransaction ? $previousTransaction->currentSoldeCredit : $client->gredit;

        // Adjust the new credit balance based on the transaction type
        if ($request->input('type') === 'achat') {
            $newGredit += $request->input('montant');
        } else {
            $newGredit -= $request->input('montant');
        }

        // Create the new transaction
        $transaction = Transaction::create([
            'type' => $request->input('type'),
            'montant' => (float) $request->input('montant'),
            'designation' => $request->input('designation'),
            'date' => $transactionDate,
            'clientId' => $request->input('clientId'),
            'currentSoldeCredit' => $newGredit,
        ]);

        // Update all subsequent transactions
        $affectedTransactions = Transaction::where('clientId', $client->id)
            ->where('date', '>', $transactionDate)
            ->orderBy('date', 'asc')
            ->get();

        foreach ($affectedTransactions as $affectedTransaction) {
            if ($affectedTransaction->type === 'achat') {
                $newGredit += $affectedTransaction->montant;
            } else {
                $newGredit -= $affectedTransaction->montant;
            }

            $affectedTransaction->update([
                'currentSoldeCredit' => $newGredit,
            ]);
        }

        // After updating all transactions, update the client's credit with the latest transaction's credit
        $client->update([
            'gredit' => $newGredit,
            'date' => $affectedTransactions->last()->date ?? $transactionDate, // Use the latest date
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
