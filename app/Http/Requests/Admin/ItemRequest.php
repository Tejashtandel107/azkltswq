<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
        $name_rules = ['required', 'max:191'];

        switch ($this->method()) {
            case 'POST':

                $name_rules = array_merge($name_rules, ['unique:items']);

            case 'PUT':
            case 'PATCH':

                $name_rules = array_merge($name_rules, ['unique:items,name,'.$this->segment(3).',item_id']);

            default:
                break;
        }

        return ['name' => implode('|', $name_rules), 'isactive' => 'required'];
    }
}
