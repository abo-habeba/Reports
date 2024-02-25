<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Absence;
use Illuminate\Http\Request;
use App\Models\Restallowance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Termwind\Components\Dd;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->countYear) {
            // الحصول على تاريخ بداية الشهر الحالي
            $startOfMonth = Carbon::now()->startOfMonth();

            // استعلام Eloquent للحصول على عدد السجلات من بداية الشهر
            $count = auth()->user()->absences->where('created_at', '>=', $startOfMonth)->count();

            return response()->json(['count' => $count]);
        }
        if (request()->countMonth) {
            // الحصول على تاريخ بداية السنة الحالية
            $startOfYear = Carbon::now()->startOfYear();

            // استعلام Eloquent للحصول على عدد السجلات من بداية السنة
            $count = auth()->user()->absences->where('created_at', '>=', $startOfYear)->count();

            return response()->json(['count' => $count]);
        }
        if (request()->countPerMonth) {
            // الحصول على تاريخ بداية السنة الحالية
            $startOfYear = Carbon::now()->startOfYear();

            // الحصول على تاريخ نهاية السنة الحالية
            $endOfYear = Carbon::now()->endOfYear();

            // استعلام Eloquent للحصول على عدد السجلات لكل شهر خلال السنة
            $countsPerMonth = auth()->user()->absences->whereBetween('created_at', [$startOfYear, $endOfYear])
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupByRaw('MONTH(created_at)')
                ->get();

            return response()->json(['countsPerMonth' => $countsPerMonth]);
        }
        $user_id = Auth::id();

        // $currentMonth = date('m');  // الحصول على الشهر الحالي
        $currentYear = date('Y');  // الحصول على السنة الحالي

        $absences = Absence::query()
            ->where('user_id', $user_id)
            ->whereYear('date', $currentYear)
            ->get();
        return $absences;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return request();
        $dailyFife = request()->dailyFife;
        // return $dailyFife;
        $startDate = Carbon::parse(request()->start_date);
        $endDate = Carbon::parse(request()->end_date);
        $dates = $startDate->daysUntil($endDate)->toArray();
        $exists = Absence::whereIn('date', $dates)->where('user_id', Auth::id())->pluck('date');
        if ($exists->isEmpty() || $dailyFife) {
            DB::beginTransaction();
            try {
                $description = request()->description;
                $Type = request()->Type;
                $rest_id = request()->rest_id;
                $user_id = Auth::id();
                // $startDate = Carbon::parse(request()->start_date);
                // $endDate = Carbon::parse(request()->end_date);
                // $dates = $startDate->daysUntil($endDate)->toArray();
                $absences = [];
                /** Types of vacations
                 *
                 * اعتيادية	Regular
                 * بدل راحة	Rest allowance
                 * عارضة	Casual
                 * اجازة عمرة	Umrah leave
                 * تجنيد	Recruitment
                 * اجازة وضع	Maternity leave
                 * اجازة تعويضية	Compensatory leave
                 * اخري	Other
                 */
                // Add regular Balance
                /** @var User */
                $user = Auth::User();
                if ($Type == 'Rest allowance') {
                    $restallowance = Restallowance::findOrFail($rest_id);
                    if ($restallowance) {
                        foreach ($dates as $date) {
                            $absence = Absence::create([
                                'description' => $description,
                                'Type' => $Type,
                                'user_id' => $user_id,
                                'date' => $date,
                                'rest_id' => $rest_id,
                            ]);
                            array_push($absences, $absence);
                        }
                        if ($dailyFife == true) {
                            if ($restallowance->state == 5) {
                                $restallowance->update([
                                    'state' => 0,
                                ]);
                            } else {
                                $restallowance->update([
                                    'state' => 5,
                                ]);
                            }
                            $restBalance = $user->restBalance;
                            $user->restBalance()->update([
                                'balance' => $restBalance->balance - 0.5,
                            ]);
                        } else {
                            $restallowance->update([
                                'state' => false,
                            ]);
                            $restBalance = $user->restBalance;
                            $user->restBalance()->update([
                                'balance' => $restBalance->balance - 1,
                            ]);
                        }
                    }
                } elseif ($Type == 'Regular' ||  $Type == 'Casual') {
                    foreach ($dates as $date) {
                        $absence = Absence::create([
                            'description' => $description,
                            'Type' => $Type,
                            'user_id' => $user_id,
                            'date' => $date,
                        ]);
                        array_push($absences, $absence);
                    }
                    // ($Type == 'Regular' ||  $Type == 'Casual')
                    $regularBalance = $user->regularBalance;
                    if ($dailyFife == true) {
                        $user->regularBalance()->update([
                            'balance' => $regularBalance->balance - 0.5,
                        ]);
                    } else {
                        $user->regularBalance()->update([
                            'balance' => $regularBalance->balance - count($absences),
                        ]);
                    }
                } else {
                    foreach ($dates as $date) {
                        $absence = Absence::create([
                            'description' => $description,
                            'Type' => $Type,
                            'user_id' => $user_id,
                            'date' => $date,
                        ]);
                        array_push($absences, $absence);
                    }
                }
                DB::commit();
                return $absences;
            } catch (\Exception $e) {
                DB::rollback();
                return "حدث خطأ أثناء حذف المستخدم. يرجى المحاولة مرة أخرى.";
            }
        } else {
            return response()->json($exists, 409);
        }
    }
    public function show(Absence $absence)
    {
        //
    }
    public function update(Request $request, Absence $absence)
    {
        //
    }
    public function destroy(Absence $absence)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            if ($absence->Type == 'Rest allowance') {
                $restallowance = Restallowance::findOrFail($absence->rest_id);
                $restallowance->update([
                    'state' => true,
                ]);
                $restBalance = $user->restBalance;
                $restBalance->balance += 1;
                $restBalance->save();
            } elseif ($absence->Type == 'Regular') {
                $regularBalance = $user->regularBalance;
                $regularBalance->balance += 1;
                $regularBalance->save();
            }
            $absence->delete();
            // return true;
            DB::commit();
            return $absence;
        } catch (\Exception $e) {
            DB::rollback();
            return "حدث خطأ أثناء حذف المستخدم. يرجى المحاولة مرة أخرى.";
        }
    }
}
