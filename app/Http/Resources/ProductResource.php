<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'qty' => $this->qty,
            'image' => $this->image ? 'images/' . $this->image : null,
            'category' => $this->category?->title,
            'created_at' => [
                'at' => $this->created_at->format('Y/m/d'),
                'human' => $this->created_at->diffForHumans(),
            ]
        ];
    }
}
