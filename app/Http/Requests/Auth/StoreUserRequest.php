<?php

namespace App\Http\Requests\Auth;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    use FailedValidationResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:dns,rfc', 'unique:users,email'],
            'password' => [
                'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ]
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Email already exists'
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key = null, $default = null);

        return array_merge($validated,  ['password' => Hash::make($this->input('password'))]);
    }

}
