<?php

namespace App\Http\Requests;

use App\Actions\Fortify\PasswordValidationRules;
use App\Enums\Certification;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** The account form an administrator fills in to create or edit a person. */
class UserRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Only administrators can create or edit accounts (App\Policies\UserPolicy).
     * Checked before validation, so others get "forbidden", not form errors.
     */
    public function authorize(): bool
    {
        $account = $this->route('user');

        return $account
            ? $this->user()->can('update', $account)
            : $this->user()->can('create', User::class);
    }

    public function rules(): array
    {
        $account = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:50', 'alpha_dash:ascii',
                Rule::unique('users', 'username')->ignore($account),
            ],
            'role' => ['required', Rule::enum(Role::class)],
            'certification' => ['required', Rule::enum(Certification::class)],
            'mobile_number' => ['nullable', 'string', 'max:20'],

            // Required for a new account. Optional when editing: leave it
            // blank to keep the current password.
            'password' => $account
                ? ['nullable', ...array_slice($this->passwordRules(), 1)]
                : $this->passwordRules(),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Usernames are stored in lowercase so "JAlvarez" and "jalvarez" are the same person.
        $this->merge(['username' => strtolower((string) $this->input('username'))]);
    }

    public function messages(): array
    {
        return [
            'username.alpha_dash' => 'Usernames can only use letters, numbers, dashes, and underscores.',
            'username.unique' => 'Someone already has that username.',
        ];
    }
}
