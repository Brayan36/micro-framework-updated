<?php

namespace Core;

interface InterfaceModel
{
    static function getAll($data = null);
    static function get($id);
    static function create($data);
    static function update($data, $id);
    static function delete($id);

}