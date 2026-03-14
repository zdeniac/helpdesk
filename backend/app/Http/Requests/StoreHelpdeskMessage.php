<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreHelpdeskMessage extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'message' => ['required', 'string', 'max:255'],
        ];

        if ($this->user()->role === UserRole::AGENT->value) {
            $rules['conversation_id'] = ['required', 'integer', 'exists:conversations,id'];
        }

        return $rules; 
    }
}
