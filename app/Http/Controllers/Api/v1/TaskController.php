<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Task\TaskCollection;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Services\Task\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function __construct(protected TaskService $service)
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response | JsonResponse
     */
    public function index()
    {
        $account = auth()->user()->tasks()
            ->paginate(sanitize_request_page_size(request('paginate')));

        return response()->json([
            "status" => true,
            "message" => "Tasks retrieved",
            "data" => new TaskCollection($account)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Task\StoreTaskRequest $request
     * @return \Illuminate\Http\Response  | JsonResponse | Response
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($request->safe()->name . Str::random(6));

        try {
            $task = DB::transaction(function() use ($data){
                return TaskService::create(auth()->user(), $data);
            });


            return response()->json([
                "status" => true,
                "message" => "Tasks created",
                "data" => new TaskResource($task)
            ]);

        } catch (\Throwable $error) {
            return $this->serverErrorResponse('Error Occurred while creating resource');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return JsonResponse
     */
    public function show(Task $task)
    {
        return response()->json([
            "status" => true,
            "message" => "Tasks created",
            "data" => new TaskResource($task)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Task\UpdateTaskRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response | JsonResponse
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();

        try {

            $task = TaskService::update($task, $data);

            return response()->json([
                "status" => true,
                "message" => "Tasks updated",
                "data" => new TaskResource($task)
            ]);

        } catch (\Throwable $error) {
            return $this->serverErrorResponse('Error Occurred while updating resource');
        }
    }

    /**
     * Delete task temporarily.
     *
     * @param  \App\Models\Task  $task
     * @return JsonResponse
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            "status" => true,
            "message" => "Tasks deleted",
        ]);
    }

    /**
     * Remove the specified resource from storage permanently.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response | JsonResponse
     */
    public function deletePermanently(Task $task)
    {
        $task->forceDelete();

        return response()->json([
            "status" => true,
            "message" => "Tasks deleted",
        ]);
    }

}
