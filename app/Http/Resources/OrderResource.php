<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $first = $this->first();

        return [
            'order_number' => $first->order_number,
            'status' => $first->status,
            'user' => $first->user->username,
            'created_at' => [
                'at' => $first->created_at->format('Y/m/d'),
                'human' => $first->created_at->diffForHumans(),
            ],
            'items' =>  OrderItemResource::collection($this),
            'total_price' => $this->sum('total'),
        ];
    }

}
