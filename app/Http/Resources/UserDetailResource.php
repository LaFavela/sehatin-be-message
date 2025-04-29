<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'height' => $this->height,
            'weight' => $this->weight,
            'birthday' => $this->birthday,
            'bmi' => $this->bmi,
            'bmr' => $this->bmr,
            'gender' => $this->gender,
            'activity' => $this->activity,
            'goal' => $this->goal,
            'user_id' => $this->user_id,
        ];
    }
}
