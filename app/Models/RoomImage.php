<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RoomImage extends Model
{
    //
    protected $fillable = [
        'room_id',
        'image',
    ];

    protected static function booted()
    {
        static::deleting(function (RoomImage $roomImage) {
            $roomImage->deleteImageFile($roomImage->image);
        });

        // Ketika data RoomImage diedit / diupdate
        static::updating(function (RoomImage $roomImage) {
            if ($roomImage->isDirty('image')) {
                $oldImage = $roomImage->getOriginal('image');

                $roomImage->deleteImageFile($oldImage);
            }
        });
    }

    private function deleteImageFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

}
