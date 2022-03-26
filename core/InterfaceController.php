<?php

namespace Core;

interface InterfaceController
{
    static function index();

    static function find($id);

    static function update($data);

    static function create();

    static function store($data);

    static function delete($id);
}