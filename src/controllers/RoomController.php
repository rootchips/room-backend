<?php

namespace App\Controllers;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;

class RoomController {
    public function index(Request $request, Response $response, $args) {
        $rooms = DB::table('rooms')->get();
        $response->getBody()->write(json_encode($rooms));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function available(Request $request, Response $response, $args) {
        // Fetch all rooms
        $rooms = DB::table('rooms')->get();

        // Check availability for each room
        foreach ($rooms as $room) {
            $bookings = DB::table('bookings')
                ->where('room_id', $room->id)
                ->where('booking_date', date('Y-m-d'))
                ->where('status', 'booked')
                ->count();

            $room->available = $bookings == 0;
        }

        $response->getBody()->write(json_encode($rooms));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $id = DB::table('rooms')->insertGetId([
            'name' => $data['name'],
            'description' => $data['description'],
            'capacity' => $data['capacity'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $response->getBody()->write(json_encode(['id' => $id]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $id = $args['id'];

        DB::table('rooms')
            ->where('id', $id)
            ->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'capacity' => $data['capacity'],
                'updated_at' => Carbon::now()
            ]);

        $response->getBody()->write(json_encode(['message' => 'Room updated']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args) {
        $id = $args['id'];
        DB::table('rooms')->where('id', $id)->delete();

        $response->getBody()->write(json_encode(['message' => 'Room deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}