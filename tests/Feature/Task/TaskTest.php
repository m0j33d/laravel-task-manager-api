<?php

namespace Tests\Feature\Task;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = Task::factory()->create();
    }

    public function test_auth_users_only_can_access_task_endpoint()
    {
        $request1 = $this->get(route('tasks.index'));
        $request1->assertUnauthorized();

        $request2 = $this->get(route('tasks.show', $this->task));
        $request2->assertUnauthorized();

        $request3 = $this->post(route('tasks.store'));
        $request3->assertUnauthorized();

        $request4 = $this->put(route('tasks.update', $this->task));
        $request4->assertUnauthorized();

        $request5 = $this->delete(route('tasks.destroy', $this->task));
        $request5->assertUnauthorized();
    }

    public function test_user_can_fetch_tasks(): void
    {
        $response = $this->actingAs($this->user)->get(route('tasks.index'));

        $response->assertStatus(200);
    }

    public function test_user_can_fetch_a_single_tasks(): void
    {
        $response = $this->actingAs($this->user)->get(route('tasks.show', $this->task));

        $response->assertStatus(200);
    }

    public function test_user_cannot_fetch_non_existence_task(): void
    {
        $response = $this->actingAs($this->user)->get(route('tasks.show', 2000));

        $response->assertStatus(404);
    }

    public function test_user_can_create_a_task(): void
    {
        $data = [
            "title" => "nice title",
            "description" => "better description"
        ];

        $response = $this->actingAs($this->user)->post(route('tasks.store'), $data);

        $response->assertStatus(201);
    }

    public function test_user_cannot_create_a_task_when_invalid_field_is_provided(): void
    {
        $data = [
            "title" => "nice title",
            "description" => null
        ];

        $response = $this->actingAs($this->user)->post(route('tasks.store'), $data);

        $response->assertUnprocessable();
    }

    public function test_user_can_update_a_task(): void
    {
        $data = [
            "title" => "updated title",
            "description" => "better description"
        ];

        $response = $this->actingAs($this->user)->put(route('tasks.update', $this->task), $data);

        $response->assertStatus(200);
    }

    public function test_user_cannot_update_a_task_when_invalid_field_is_provided(): void
    {
        $data = [
            "title" => null,
            "description" => null
        ];

        $response = $this->actingAs($this->user)->put(route('tasks.update', $this->task), $data);

        $response->assertUnprocessable();
    }

    public function test_user_can_delete_a_task_temporarily(): void
    {
        $response = $this->actingAs($this->user)->delete(route('tasks.destroy', $this->task));

        $response->assertStatus(200);
    }

    public function test_user_can_delete_a_task_permanently(): void
    {
        $response = $this->actingAs($this->user)->delete(route('task.force.delete', $this->task));

        $response->assertStatus(200);
    }
}
