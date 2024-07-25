<?php
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\AuthMiddleware;
use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\RoomController;


// Public routes
$app->post('/login', [AuthController::class, 'login']);
$app->post('/register', [AuthController::class, 'register']);

// Protected routes
$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/rooms', [RoomController::class, 'index']);
    $group->get('/available', [RoomController::class, 'available']);
    $group->post('/book', [BookingController::class, 'create']);
    $group->get('/bookings', [BookingController::class, 'index']);
    $group->post('/cancel/{id}', [BookingController::class, 'cancel']);
})->add(new AuthMiddleware());

// Admin routes
$app->group('/admin', function (RouteCollectorProxy $group) {
    $group->post('/rooms', [RoomController::class, 'create']);
    $group->delete('/rooms/{id}', [RoomController::class, 'delete']);
    $group->put('/rooms/{id}', [RoomController::class, 'update']);
})->add(new AuthMiddleware('admin'));

$app->addErrorMiddleware(true, true, true);
