<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'        => ['required', 'email:rfc,dns', 'max:191'],
            'notify_email' => ['nullable', 'email:rfc', 'max:191'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Veuillez entrer une adresse email.',
            'email.email'    => 'Veuillez entrer une adresse email valide.',
        ];
    }
}
