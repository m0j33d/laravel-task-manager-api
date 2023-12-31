<?php

namespace App\Http\Requests\Task;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    use FailedValidationResponse;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:tasks,title,NULL,id,user_id,' . auth()->id()],
            'description' => ['required', 'string'],
            'category' => ['sometimes', 'exists:categories,id']
        ];
    }
}
