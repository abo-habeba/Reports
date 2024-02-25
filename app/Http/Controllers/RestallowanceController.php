<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restallowance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RestallowanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Auth::id();

        // $currentMonth = date('m');  // الحصول على الشهر الحالي
        $currentYear = date('Y');  // الحصول على السنة الحالي

        $restAllowance = Restallowance::query()
            ->where('user_id', $user_id)
            ->whereYear('date', $currentYear)
            ->get();
        if ($restAllowance && $restAllowance->isNotEmpty()) {
            # code...
            return $restAllowance;
        } else {
            return false;
        }
    }
    public function store(Request $request)
    {
        $dated = $request->input('date');
        $exists = Restallowance::where('date', $dated)->where('user_id', Auth::id())->pluck('date');
        if ($exists->isEmpty()) {
            DB::beginTransaction();
            try {
                $description = $request->input('description');
                $user_id = Auth::id();
                $dated = $request->input('date');
                /** @var User */
                $user = Auth::User();
                $restallowance = Restallowance::create([
                    'description' => $description,
                    'user_id' => $user_id,
                    'date' => $dated,
                ]);
                $restBalance = $user->restBalance;
                $user->restBalance()->update([
                    'balance' => $restBalance->balance + 1,
                ]);
                DB::commit();
                return $restallowance;
            } catch (\Exception $e) {
                DB::rollback();
                return "حدث خطأ أثناء حذف المستخدم. يرجى المحاولة مرة أخرى.";
            }
        } else {
            return response()->json($exists, 409);
        }
    }
    public function show(Restallowance $restallowance)
    {
        //
    }
    public function update(Request $request, Restallowance $restallowance)
    {
        $restallowance->state = !$restallowance->state;
        $restallowance->save();
    }
    public function destroy(Restallowance $restallowance)
    {
        // return $restallowance->state;
        DB::transaction(function () use ($restallowance) {
            // $user = auth()->user();
            if ($restallowance->state) {
                $restBalance = auth()->user()->restBalance;
                $restBalance->balance -= 1;
                $restBalance->save();
                $restallowance->delete();
                return true;
            } else {
                return response()->json(false);
            }
        });
    }
}
