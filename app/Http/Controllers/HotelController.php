<?php

namespace App\Http\Controllers;

use App\Services\HotelBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelController extends Controller
{
    private const SESSION_KEY = 'hotel_room_status';

    public function index(): View
    {
        return view('hotel.index');
    }

    public function state(Request $request): JsonResponse
    {
        $map = $this->getStatusMap($request);

        return response()->json([
            'rooms' => $this->buildLayout($map),
            'maxBooking' => HotelBookingService::MAX_ROOMS_PER_BOOKING,
        ]);
    }

    public function book(Request $request, HotelBookingService $service): JsonResponse
    {
        $validated = $request->validate([
            'n' => ['required', 'integer', 'min:1', 'max:'.HotelBookingService::MAX_ROOMS_PER_BOOKING],
        ]);

        $map = $this->getStatusMap($request);
        foreach ($map as $room => $status) {
            if ($status === HotelBookingService::STATUS_BOOKED) {
                $map[$room] = HotelBookingService::STATUS_FREE;
            }
        }

        $picked = $service->selectRooms($map, (int) $validated['n']);
        if ($picked === null) {
            return response()->json([
                'ok' => false,
                'message' => 'Not enough free rooms for this booking.',
            ], 422);
        }

        foreach ($picked as $room) {
            $map[$room] = HotelBookingService::STATUS_BOOKED;
        }
        $request->session()->put(self::SESSION_KEY, $map);

        return response()->json([
            'ok' => true,
            'booked' => $picked,
            'diameter' => HotelBookingService::setDiameter($picked),
            'rooms' => $this->buildLayout($map),
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $request->session()->forget(self::SESSION_KEY);

        return response()->json([
            'ok' => true,
            'rooms' => $this->buildLayout(HotelBookingService::defaultStatusMap()),
        ]);
    }

    public function random(Request $request): JsonResponse
    {
        $map = HotelBookingService::defaultStatusMap();
        foreach ($map as $room => $unused) {
            if (random_int(0, 100) < 42) {
                $map[$room] = HotelBookingService::STATUS_BLOCKED;
            } else {
                $map[$room] = HotelBookingService::STATUS_FREE;
            }
        }
        $request->session()->put(self::SESSION_KEY, $map);

        return response()->json([
            'ok' => true,
            'rooms' => $this->buildLayout($map),
        ]);
    }

    /** @return array<int, string> */
    private function getStatusMap(Request $request): array
    {
        $stored = $request->session()->get(self::SESSION_KEY);
        if (! is_array($stored)) {
            return HotelBookingService::defaultStatusMap();
        }
        $defaults = HotelBookingService::defaultStatusMap();
        foreach ($defaults as $room => $status) {
            if (isset($stored[$room]) && in_array($stored[$room], [
                HotelBookingService::STATUS_FREE,
                HotelBookingService::STATUS_BLOCKED,
                HotelBookingService::STATUS_BOOKED,
            ], true)) {
                $defaults[$room] = $stored[$room];
            }
        }

        return $defaults;
    }

    /**
     * Wireframe: top row 7 rooms (floor 10), then floors 9..1 with 10 rooms each; lift column on the left.
     *
     * @param  array<int, string>  $map
     */
    private function buildLayout(array $map): array
    {
        $rows = [];
        $rows[] = [
            'floor' => 10,
            'rooms' => $this->rowRooms($map, range(1001, 1007)),
        ];
        for ($f = 9; $f >= 1; $f--) {
            $nums = [];
            for ($u = 1; $u <= 10; $u++) {
                $nums[] = $f * 100 + $u;
            }
            $rows[] = [
                'floor' => $f,
                'rooms' => $this->rowRooms($map, $nums),
            ];
        }

        return $rows;
    }

    /**
     * @param  array<int, string>  $map
     * @param  list<int>  $numbers
     * @return list<array{number:int,status:string}>
     */
    private function rowRooms(array $map, array $numbers): array
    {
        $out = [];
        foreach ($numbers as $n) {
            $out[] = [
                'number' => $n,
                'status' => $map[$n] ?? HotelBookingService::STATUS_FREE,
            ];
        }

        return $out;
    }
}
