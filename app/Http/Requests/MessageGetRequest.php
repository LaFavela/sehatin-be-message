<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use function Laravel\Prompts\error;

class MessageGetRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'string|nullable|exists:users,id',
            'per_page' => 'integer|nullable',
            'sort_by' => ['string', 'nullable', Rule::in([
                'content',
                'user_id'
            ])],
            'sort_direction' => ['string', 'nullable', Rule::in(['asc', 'desc'])],
        ];
    }

    protected function passedValidation(): void
    {
        $userId = $this->header('X-User-ID');
        $role = $this->header('X-User-Role');

        if ($userId && $role != 'admin') {
            $this->merge(
                ['user_id' => $userId]
            );
        }
    }

    // Failed validation method
    public $validator = null;

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $this->validator = $validator;
    }
}
