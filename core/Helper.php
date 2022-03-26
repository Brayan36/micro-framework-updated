<?php

namespace Core;

class Helper
{
    public static function url($url = ''): string
    {

        $scheme = $_SERVER['REQUEST_SCHEME'] . '://';
        $requestUri = $_SERVER['REQUEST_URI'];
        $host = $_SERVER['HTTP_HOST'];

        $arrayUri = explode('/', $requestUri);
        $public = false;
        foreach ($arrayUri as $k => $value) {
            if ($value == 'public') $public = $k;
        }

        if ($public !== false) {
            $requestUri = [];
            foreach ($arrayUri as $k => $value) {
                if ($k <= (int) $public) $requestUri[] = $value;
            }
            $requestUri[] = '';
            $requestUri = implode('/', $requestUri);
        } else {
            $requestUri = '/';
        }

        return $scheme . $host . $requestUri . $url;

    }

    public static function layouts($type): string
    {
        return self::dir() . 'layouts/' . $type . '.php';
    }

    private static function dir(): string
    {
        $pathBase = __DIR__;
        $pos = stripos($pathBase, bm_project_name);
        return substr($pathBase, 0, $pos) . bm_project_name . '/public/resources/';
    }

    public static function redirectTo($url)
    {
        $scheme = $_SERVER['REQUEST_SCHEME'] . '://';
        $host = $_SERVER['HTTP_HOST'];
        header("Location: " . $scheme . $host . $url);
        die();
    }
}