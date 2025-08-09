<?php

declare(strict_types=1);

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

final class CreateTagRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100', 'unique:tags,name'],
            'description' => ['nullable', 'string'],
        ];
    }
}
