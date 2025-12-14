<?php

namespace App\Modules\UserManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = \App\Models\User::find($this->route('user'));
        return $this->user()->can('update', $user);
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role_id' => ['sometimes', 'required', 'exists:roles,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
