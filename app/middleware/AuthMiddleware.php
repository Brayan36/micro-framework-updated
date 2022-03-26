<?php

namespace App\Middleware;

use Core\Helper;
use Core\Middleware;

class AuthMiddleware extends Middleware
{
    public function handle()
    {
        if (empty($this->session)) {
            session_unset();     // unset $_SESSION variable
            session_destroy();   // destroy session data
            $error = "session expirada o no encontrada";
            Helper::redirectTo('/login?error=' . $error);
        }
    }

}