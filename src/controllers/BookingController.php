<?php

namespace App\Controllers;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;

class BookingController {
    public function index(Request $request, Response $response, $args) {
        
        $bookings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.name as room_name')
            ->get();
        
        $response->getBody()->write(json_encode($bookings));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $id = DB::table('bookings')->insertGetId([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $data['room_id'],
            'booking_date' => $data['booking_date'],
            'status' => 'booked',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $response->getBody()->write(json_encode(['id' => $id]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function cancel(Request $request, Response $response, $args) {
        $id = $args['id'];

        DB::table('bookings')
            ->where('id', $id)
            ->update([
                'status' => 'cancelled',
                'updated_at' => Carbon::now()
            ]);

        $response->getBody()->write(json_encode(['message' => 'Booking cancelled']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}