<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'username' => [
                'required',
                'string',
                'unique:users,username',
            ],
            'phone_number' => [
                'required',
                'string',
                'unique:users,phone_number',
            ],
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'unique:users,email',
            ],
            'password' => 'required|string',
            'password_confirmation' => 'required|string|same:password',
        ];
    }
}
