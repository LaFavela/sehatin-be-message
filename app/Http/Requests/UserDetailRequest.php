<?php

namespace App\Http\Requests;

use App\Enum\Activity;
use App\Enum\Gender;
use App\Enum\Goal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserDetailRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'birthday' => 'required|date',
            'bmi' => 'nullable|numeric',
            'bmr' => 'nullable|numeric',
            'gender' => ['required', Rule::enum(Gender::class)],
            'activity' => ['required', Rule::enum(Activity::class)],
            'goal' => ['required', Rule::enum(Goal::class)],
            'user_id' => 'required|exists:users,id|unique:user_details'
        ];
    }

    // Failed validation method
    public $validator = null;

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $this->validator = $validator;
    }
}
