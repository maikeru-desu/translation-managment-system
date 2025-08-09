<?php

declare(strict_types=1);

namespace App\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;

final class ExportTranslationRequest extends FormRequest
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
            'locale_ids' => ['sometimes', 'array'],
            'locale_ids.*' => ['integer', 'exists:locales,id'],
            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ];
    }

    /**
     * Get default values for request parameters.
     *
     * @return array
     */
    protected function prepareForValidation(): void
    {
        // Default format to 'flat' if not provided
        if (! $this->has('format')) {
            $this->merge(['format' => 'flat']);
        }

        // Default include_tags to false if not provided
        if (! $this->has('include_tags')) {
            $this->merge(['include_tags' => false]);
        }
    }
}
