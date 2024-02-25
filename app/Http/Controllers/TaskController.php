<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all() ;
        foreach ($tasks as $key ) {
           $key->user ;
           $key->station ;
        }
        return Response()->json([
         'tasks' => $tasks
     ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'ppm' => ['required'],
            'quantity' => ['required'],
            'cont_hours' => ['required'],
            'actual_time' => ['required'],
            'slices_ton' => ['required'],
            'shift' => ['required'],
            'name' => ['required'],
            'description' => ['required'],
            'station_id' => ['required'],
            'user_id' => ['required'],
            ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ]);
        }
        $task = Task::create(request()->all());
        if (!$task) {
            return Response()->json([
                'message' => 'no tasks create'
            ]);
        }
        return Response()->json([
            'tasks' => $task
        ]);
        
    }
    public function show(string $id)
    {
        $task = Task::find($id);
             $task->user ;
             $task->station ;
    
        if ($task) {
            return response()->json([
                'task' => $task,
            ]);
        } else {
        return Response()->json([
            'message' => 'no Station'
        ]);
        }
    }
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),
        [
            'ppm' => ['required'],
            'quantity' => ['quantity'],
            'cont_hours' => ['required'],
            'actual_time' => ['actual_time'],
            'slices_ton' => ['slices_ton'],
            'shift' => ['shift'],
            'name' => ['name'],
            'description' => ['description'],
            'station_id' => ['station_id'],
            'user_id' => ['user_id'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ]);
        }
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'The station does not exist',
            ]);
        }
        $task->update(request()->all());
        if ($task) {
        return Response()->json([
            'tasks' => $task
        ]);
        }
        return response()->json([
            'message' => 'no tasks update',
        ]);
    }
    public function destroy(string $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'The station does not exist',
            ]);
        }
        $task->delete();
        return Response()->json([
            'message' => 'The station has been deleted'
        ]);

    }
}