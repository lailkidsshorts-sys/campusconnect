<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Normalize name from fname and lname if provided
        $name = $this->input('name');
        if (! $name && ($this->filled('fname') || $this->filled('lname'))) {
            $name = trim($this->input('fname', '') . ' ' . $this->input('lname', ''));
        }

        // Normalize email and roll number (case sensitivity handling)
        $this->merge([
            'name' => $name ? ucwords(strtolower(trim($name))) : null,
            'email' => $this->filled('email') ? strtolower(trim($this->input('email'))) : null,
            'roll_no' => $this->filled('roll_no') ? strtoupper(trim($this->input('roll_no'))) : ($this->filled('cnic') ? trim($this->input('cnic')) : null),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'roll_no' => ['required', 'string', 'max:100', 'unique:users,roll_no'],
            'email' => ['required', 'string', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Student name is required.',
            'roll_no.required' => 'University Roll Number is required for student verification.',
            'roll_no.unique' => 'This roll number is already registered in the system.',
            'email.unique' => 'An account with this email address already exists.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 6 characters.',
        ];
    }
}
