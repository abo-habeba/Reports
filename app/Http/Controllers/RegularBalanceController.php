<?php

namespace App\Http\Controllers;

use App\Models\RegularBalance;
use Illuminate\Http\Request;

class RegularBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        return RegularBalance::where('user_id', $user_id)->first();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $regularBalance = $user->regularBalance;
        $regularBalance->balance = request()->balance;
        $regularBalance->save();
        return $regularBalance;
    }

    /**
     * Display the specified resource.
     */
    public function show(RegularBalance $regularBalance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegularBalance $regularBalance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegularBalance $regularBalance)
    {
        //
    }
}
