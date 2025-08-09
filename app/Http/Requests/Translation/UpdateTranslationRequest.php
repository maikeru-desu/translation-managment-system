<?php

declare(strict_types=1);

namespace App\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTranslationRequest extends FormRequest
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
            'locale_id' => ['sometimes', 'integer', 'exists:locales,id'],
            'key' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }
}
