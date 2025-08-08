<?php

declare(strict_types=1);

namespace App\Http\Requests\Locale;

use Illuminate\Foundation\Http\FormRequest;

final class CreateLocaleRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:10', 'unique:locales,code'],
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ];
    }
}
