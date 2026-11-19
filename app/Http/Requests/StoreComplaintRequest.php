<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isStudent();
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'issue_type_id' => ['required', 'exists:issue_types,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'priority' => ['required', 'in:low,medium,high'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a complaint category.',
            'issue_type_id.required' => 'Please select an issue type.',
            'subject.required' => 'Complaint subject is required.',
            'description.min' => 'Description must be at least 10 characters long.',
            'attachment.max' => 'Attachment size must not exceed 2MB.',
            'attachment.mimes' => 'Attachment must be a valid image file (JPG, JPEG, or PNG).',
        ];
    }
}
