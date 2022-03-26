<?php

use App\Controller\Auth\AuthController;
use App\Controller\User\UserController;
use App\Controller\DashboardController;
use App\Middleware\AuthMiddleware;
use Core\Router;

Router::get('/', fn() => $helper->redirectTo('/login'));

Router::get('/login', fn() => AuthController::index());
Router::post('/login', fn() => AuthController::login($_REQUEST));

Router::middleware(AuthMiddleware::class)->get('/register', fn() => AuthController::create());
Router::post('/register', fn() => AuthController::store($_REQUEST));

Router::get('/logout', fn() => AuthController::logout());
Router::post('/logout', fn() => AuthController::logout());

Router::middleware(AuthMiddleware::class)->get('/user', fn() => UserController::index());

Router::get('/dashboard', fn() => DashboardController::index());