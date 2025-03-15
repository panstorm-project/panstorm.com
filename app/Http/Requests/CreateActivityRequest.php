<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\EventType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateActivityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
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
}
