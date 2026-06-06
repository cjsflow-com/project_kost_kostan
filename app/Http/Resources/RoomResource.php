<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            'room_number' => $this->room_number,
            'title' => $this->title,
            'description' => $this->description,
            'price_per_month' => (int)$this->price_per_month,
            'room_size' => $this->room_size,
            'floor' => $this->floor,
            'capacity' => $this->capacity,
            'status_id' => $this->status_id,
            'status_name' => $this->status_name, // Menggunakan accessor untuk mendapatkan nama status

            'thumbnail' => $this->images->first() ? asset('storage/' . $this->images->first()->image_path) : null,

            // Relasi
            'facilities'=> $this->whenLoaded('facilities'),
            'images' => $this->whenLoaded('images'),

            // optional timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
