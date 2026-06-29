<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RoomFacility extends Model
{
    //
    protected $fillable = [
        'name',
        'icon',
    ];


    protected static function booted()
    {
        static::deleting(function ($roomFacility) {
            // Delete the icon file from storage
            $roomFacility->deleteIconFile($roomFacility->icon);
        });

        // Jalan ketika data diedit/update
        static::updating(function ($roomFacility) {
            // Cek apakah field icon berubah
            if ($roomFacility->isDirty('icon')) {
                // Ambil icon lama dari database
                $oldIcon = $roomFacility->getOriginal('icon');

                // Hapus icon lama
                $roomFacility->deleteIconFile($oldIcon);
            }
        });
    }

    private function deleteIconFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
