<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'avatar' => $this->avatar,
            'is_admin' => $this->role === 'admin' ? '1' : '0',
            'gender' => $this->gender,
            'address' => $this->address,
            'phone' => $this->phone,
            'created_at' => [
                'at' => $this->created_at->format('Y/m/d'),
                'human' => $this->created_at->diffForHumans(),
            ]
        ];
    }
}
