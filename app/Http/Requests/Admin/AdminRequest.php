<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
        switch ($this->method()) {
            case 'POST':

                return [
                    'username' => 'required|max:255|unique:users',
                    'email' => 'required|email|max:255|unique:users',
                ];

            case 'PUT':
            case 'PATCH':

                return [
                    'username' => 'required|max:255|unique:users,username,'.$this->segment(4).',user_id',
                    'email' => 'required|email|max:255|unique:users,email,'.$this->segment(4).',user_id',
                ];

            default:
                break;
        }
    }
}
