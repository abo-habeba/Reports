<?php

namespace App\Http\Controllers;

use App\Models\RestBalance;
use Illuminate\Http\Request;

class RestBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        return RestBalance::where('user_id', $user_id)->first();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $restBalance = $user->restBalance;
        $restBalance->balance += request()->balance;
        $restBalance->save();
        return $restBalance;
    }

    /**
     * Display the specified resource.
     */
    public function show(RestBalance $restBalance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RestBalance $restBalance)
    {
        // $restBalance->update([
        //     'field_name' => $request->input('field_name'),
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RestBalance $restBalance)
    {
        //
    }
}
