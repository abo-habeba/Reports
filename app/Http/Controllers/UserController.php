<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Report;
use App\Models\Station;
use App\Models\RestBalance;
use Illuminate\Http\Request;
use App\Models\RegularBalance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('tasks')->get();
        foreach ($users as $user) {
            $user->tasks_count;
        }
        return response()->json($users);
    }
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required'],
                'email' => ['required', 'email', 'unique:users'],
                'phone' => ['required'],
                'Job_title' => ['required'],
                'password' => ['required', 'min:8'],
                'station_id' => ['required']
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 401);
        };
        $station = Station::find(request()->station_id);
        if (!$station) {
            return response()->json([
                'message' => 'There is no ID matching this station',
            ], 401);
        }
        DB::beginTransaction();
        $user = User::create(request(['name', 'email', 'Job_title', 'phone']) + ['password' => bcrypt(request()->password)]);
        //add many to many relation
        $user->stations()->attach(request()->station_id);
        $segularBalance = RegularBalance::create(['user_id' => $user->id]);
        $restBalance = RestBalance::create(['user_id' => $user->id]);

        if (!$restBalance && !$segularBalance) {
            DB::rollback();
            return 'failed to add Rest Balance or Regular Balance';
        }
        DB::commit();

        return Response()->json($user);
    }
    public function update($id)
    {
        $user = User::findOrFail($id);

        $user->update(request()->only('name', 'email', 'phone'));

        if (request()->password) {
            $user->update(['password' => bcrypt(request()->password)]);
        }

        return $user->fresh();
    }
    public function show($userId)
    {
        $user = User::findOrFail($userId);
        return $user->load('stations.reports.user', 'stations.reports.comments');
    }
    public function getUserReports($userId)
    {
        $user = User::find($userId);
        $stationIds = $user->stations->pluck('id');
        $reports = Report::whereIn('station_id', $stationIds)->with('user', 'station', 'comments.user')->orderBy('created_at', 'DESC')->get();
        return response()->json($reports);
    }
    public function authCheck(Request $request)
    {
        return 'authCheck ok';
        // $user = $request->user();
        // $requestTokenId = intval(explode("|", request()->bearerToken())[0]);
        // $tokenId = $user->tokens()->where('id', $requestTokenId)->first();
        // if ($tokenId) {
        //     if ($tokenId->id == $requestTokenId) {
        //         return response()->json(true);
        //     } else {
        //         $request->session()->invalidate();
        //         return response()->json(false);
        //     }
        // } else {
        //     $request->session()->invalidate();
        //     return response()->json(false);
        // }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            // $user = auth()->user();
            // حذف العلاقات المرتبطة بالمستخدم
            $user->stations()->detach();
            $user->tasks()->delete();
            $user->reports()->delete();
            $user->absences()->delete();
            $user->regularBalance()->delete();
            $user->restBalance()->delete();
            $user->restallowance()->delete();
            $request->user()->currentAccessToken()->delete();
            // حذف المستخدم نفسه
            $user->delete();
            DB::commit();

            return "تم حذف المستخدم بنجاح.";
        } catch (\Exception $e) {
            DB::rollback();
            return "حدث خطأ أثناء حذف المستخدم. يرجى المحاولة مرة أخرى.";
        }
    }
}