<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'body' => $this->body,
            'sender_id' => $this->sender_id,
            'sender' => $this->sender->username,
            'receiver_id' => $this->receiver_id,
            'receiver' =>  $this->receiver->username,
            'created_at' => [
                'at' => $this->created_at->format('Y/m/d'),
                'human' => $this->created_at->diffForHumans(),
            ]
        ];
    }
}
