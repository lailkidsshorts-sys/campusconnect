<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComplaintStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isDeptAdmin() || $this->user()->isSuperAdmin());
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending,in_progress,resolved,rejected'],
            'remark' => ['required', 'string', 'min:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Please select a new status.',
            'remark.required' => 'An administrative remark / explanation is required.',
            'remark.min' => 'Administrative remark must be at least 5 characters long.',
        ];
    }
}
