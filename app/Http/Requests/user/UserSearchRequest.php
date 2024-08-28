<?php

namespace App\Http\Requests\user;

use App\Common\RoleConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserSearchRequest extends FormRequest
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
        return  [
            'name' => [
                'sometimes',
            ],
            'username' => [
                'sometimes',
            ],
            'email' => [
                'sometimes',
            ],
            'phone_number' => [
                'sometimes',
            ],

        ];
    }
}
