<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowRequest extends FormRequest
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
            'quantity' => 'required|numeric|min:1',
            'from' => 'required|exists:sections,id',
            'type' => 'required|in:personal,impersonal',  // Validates the type to be either personal or impersonal
            'parent_section' => 'required_if:type,impersonal|not_in:'. $this->from,  // Requires parent_section if type is 'impersonal'
            'child_section' => 'nullable',  // No validation for child_section, as it's not required
        ];
    }
}
