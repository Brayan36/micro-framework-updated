<?php

namespace App\Controller\User;

use App\Controller\Controller;
use Core\View;

class UserController extends Controller
{
    public static function index()
    {
        echo 'alcanzo antes de iniciar el middleware';
        die();
        $view = new View('User/index');
        $view->with('data', 'esta es la pagina de usuario');
        return $view;
    }
}