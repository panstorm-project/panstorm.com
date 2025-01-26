<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\EventType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class CreateActivityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'events' => ['required', 'array'],
            'events.*.type' => ['required', 'string', Rule::enum(EventType::class)],
            'events.*.payload' => ['required', 'array'],
            'events.*.payload.url' => [
                Rule::requiredIf(fn (): bool => $this->input('events.*.type') === EventType::View),
                'string',
            ],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();
        $errors->add('events', 'The events field is invalid.');

        $response = response()->json([
            'errors' => $validator->errors(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
