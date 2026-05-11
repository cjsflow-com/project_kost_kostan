<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Models\GeneralFacility;
use App\Models\Room;
use BaseResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    //
    public function show($id)
    {
        $room = Room::with(['facilities', 'images'])->find($id);
        if (!$room) {
            return BaseResponse::error('Kamar tidak ditemukan', 404);
        }

        $general_facilities = GeneralFacility::query()->latest()->limit(5)->get();

        return BaseResponse::success(array_merge(
            (new RoomResource($room))->resolve(),
            [
                'general_facilities' => $general_facilities,
            ]
        ),
        'Detail kamar berhasil diambil'
        );
    }

    public function index()
    {
        $rooms = Room::with(['facilities', 'images'])->latest()->paginate(10);
        return BaseResponse::success([
            'rooms' => RoomResource::collection($rooms),
            'pagination' => [
                'current_page' => $rooms->currentPage(),
                'last_page' => $rooms->lastPage(),
                'per_page' => $rooms->perPage(),
                'total' => $rooms->total(),
            ],
            'Daftar kamar berhasil diambil'
        ]);
    }
}
