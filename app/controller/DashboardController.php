<?php

namespace App\Controller;

use Core\View;

class DashboardController extends Controller
{
    public static function index()
    {
        $view = new View('Dashboard/index');
        $view->with('data', 'esta es la pagina del dashboard');
        return $view;
    }
}