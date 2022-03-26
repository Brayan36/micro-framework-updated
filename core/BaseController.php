<?php

namespace Core;

abstract class BaseController implements InterfaceController
{

    static function index()
    {
        // TODO: Implement index() method.
    }

    static function find( $id )
    {
        // TODO: Implement find() method.
    }

    static function update( $data )
    {
        // TODO: Implement update() method.
    }

    static function create()
    {
        // TODO: Implement create() method.
    }

    static function store( $data )
    {
        // TODO: Implement store() method.
    }

    static function delete( $id )
    {
        // TODO: Implement delete() method.
    }

    private function __destruct() { die(); }
}