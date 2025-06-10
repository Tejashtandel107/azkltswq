<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'companyname' => 'sometimes|required|max:255',
            'last_invoice_date' => 'sometimes|required',
            'invoice_limit' => 'sometimes|required|numeric|min:1',
        ];
    }
}
