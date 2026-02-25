<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
            'role' => ['nullable', 'string', 'in:admin,brand,creator,agency,studio_owner,customer'],
        ];
    }

    /**
     * Attempt login and attach role check. Throw ValidationException on failure.
     */
    public function authenticate(): void
    {
        if (! auth()->attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $user = auth()->user();

        if ($this->filled('role') && ! $user->hasRole($this->role)) {
            auth()->logout();
            $primary = $user->primaryRole();
            $roleLabel = $primary ? $primary->name : 'user';
            throw ValidationException::withMessages([
                'email' => ["This account is registered as {$roleLabel}. Sign in with the correct account type."],
            ]);
        }
    }
}
