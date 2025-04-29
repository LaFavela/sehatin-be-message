<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
            'content' => 'required|string',
        ];
    }

    /**
     * Prepare the request for validation.
     *
     * @return void
     */
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
