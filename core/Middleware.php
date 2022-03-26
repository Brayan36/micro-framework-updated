<?php

namespace Core;

class Middleware
{

    protected $session = null;

    public function __construct()
    {
        session_start();
        if (!is_null($_SESSION)) $this->session = $_SESSION;
    }
}