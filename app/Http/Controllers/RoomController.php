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

    public function index(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_order', 'desc');

        $allowedSortBy = [
            'created_at',
            'title',
            'room_number',
            'price_per_month',
        ];

        $allowedSortDir = [
            'asc',
            'desc',
        ];

        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortDir, $allowedSortDir)) {
            $sortDir = 'desc';
        }


         $rooms = Room::with(['facilities', 'images'])
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('room_number', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        })
        ->orderBy($sortBy, $sortDir)
        ->paginate(10);
        return BaseResponse::success([
            'rooms' => RoomResource::collection($rooms),
            'pagination' => [
                'current_page' => $rooms->currentPage(),
                'last_page' => $rooms->lastPage(),
                'per_page' => $rooms->perPage(),
                'total' => $rooms->total(),
            ],
        ]);
    }


}
