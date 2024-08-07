<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Carbon\Carbon;

class StatisticController extends Controller
{
    /**
     * Fetch statistics including total credit, credit older than two months, and top 10 credit clients.
     */
    public function index()
    {
        try {
            // Fetch total credit
            $totalCredit = Client::sum('gredit');

            // Fetch total credit older than two months
            $twoMonthsAgo = Carbon::now()->subMonths(2);
            $totalCreditOlderThanTwoMonths = Client::where('date', '<', $twoMonthsAgo)->sum('gredit');

            // Fetch top 10 credit clients
            $topClients = Client::orderBy('gredit', 'desc')->take(10)->get();
            $topCreditClientsTotal = $topClients->sum('gredit');

            return response()->json([
                'totalCredit' => $totalCredit,
                'totalCreditOlderThanTwoMonths' => $totalCreditOlderThanTwoMonths,
                'topCreditClientsTotal' => $topCreditClientsTotal,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch metrics'], 500);
        }
    }
}
