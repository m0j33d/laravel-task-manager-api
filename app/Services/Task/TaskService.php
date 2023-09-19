<?php

namespace App\Services\Task;

use App\Models\Task;
use App\Models\User;

class TaskService
{

    /**
     * Create new Task
     *
     * @param array \App\Models\User $user
     * @param array $data
     *
     * @return \App\Models\Task
     */
    public function create(?User $user, $data): Task
    {
        return $user->tasks()->create($data);
    }

    /**
     * Update Task
     *
     * @param \App\Model\Task $task
     * @param array $data
     *
     * @return \App\Model\Task
     */
    public static function update(Task $task, $data): bool
    {
        return $task->update($data);
    }
}
