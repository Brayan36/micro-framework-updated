<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Model\User\Auth;
use Core\Helper;
use Core\View;

class AuthController extends Controller
{
    public static function index()
    {
        $view = new View('Auth/login');
        $view->with('data', 'esta es la pagina del login');
        return $view;
    }

    public static function create()
    {
        return View::make('Auth/register');
    }

    public static function store($data)
    {

    }

    public static function login($data)
    {
        $auth = new Auth();
        $user = $auth->attempt($data);

        $error = 'Credenciales inválidas!';
        if (is_null($user)) Helper::redirectTo("/login?error=$error");

        Helper::redirectTo('/dashboard');
    }

    public static function logout()
    {
        echo 'logout!';
    }
}