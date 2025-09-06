<?php
use App\Routers\Router as Router;
use App\Middlewares\AuthMiddleware;

// use Controllers
use App\Controllers\AuthController;
use App\Controllers\DestinationController;
use App\Controllers\RoomController;
use App\Controllers\WeatherController;


// ایجاد یک نمونه از میدلور
$authMiddleware = new AuthMiddleware();
$request = getTokenFromRequest();

$response = $authMiddleware->handle($request);

// if(!$response) exit();

$router = new Router();

// Define routes
$router->post('v1','/login', AuthController::class, 'login');
$router->post('v1','/register', AuthController::class, 'register');
$router->post('v1','/verify', AuthController::class, 'verify');
$router->get('v1','/test', AuthController::class, 'test');

// Weathers
$router->get('v1','/weathers', WeatherController::class, 'index', "owners");
$router->get('v1','/weathers/{id}', WeatherController::class, 'get', "owners");
$router->post('v1','/weathers', WeatherController::class, 'store', "owners");
$router->put('v1','/weathers/{id}', WeatherController::class, 'update', "owners");
$router->delete('v1','/weathers/{id}', WeatherController::class, 'destroy', "owners");

// Destinations
$router->get('v1','/destinations', DestinationController::class, 'index', "owners");
$router->get('v1','/destinations/{id}', DestinationController::class, 'get', "owners");
$router->post('v1','/destinations', DestinationController::class, 'store', "owners");
$router->put('v1','/destinations/{id}', DestinationController::class, 'update', "owners");
$router->delete('v1','/destinations/{id}', DestinationController::class, 'destroy', "owners");

// Rooms
$router->get('v1','/rooms', RoomController::class, 'index', inaccess:['guest']);
$router->post('v1','/rooms', RoomController::class, 'store', inaccess:['guest']);