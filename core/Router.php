<?php
namespace Core;

class Router {

    protected $requestUri;
    protected $routes;
    private static $this;
    protected $middlewares = [];

    const GET_PARAMS_DELIMITER = '?';

    public function __construct($requestUri)
    {
        self::$this = $this;
        $this->routes = [];
        $this->setRequestUri($requestUri);
    }

    public function setRequestUri($requestUri)
    {
        if (strpos($requestUri, self::GET_PARAMS_DELIMITER))
        {
            $requestUri = strstr($requestUri, self::GET_PARAMS_DELIMITER, true);
        }

        $arrayUri = explode('/', $requestUri);
        $public = false;
        foreach ($arrayUri as $k => $value) {
            if ($value == 'public') $public = $k;
        }
        if ($public !== false) {
            $requestUri = [''];
            foreach ($arrayUri as $k => $value) {
                if ($k > (int) $public) $requestUri[] = $value;
            }
            $requestUri = implode('/', $requestUri);
        }
        $this->requestUri = $requestUri;
    }

    public function getRequestUri()
    {
        return $this->requestUri;
    }

    public static function get($uri, $closure)
    {
        $route = new Route($uri, $closure);
        self::$this->routes[] = [
           'route' => $route,
            'middleware' => self::$this->middlewares
        ];
        self::$this->middlewares = [];
    }

    public static function post($uri, $closure)
    {
        $route = new Route($uri, $closure, 'POST');
        self::$this->routes[] = [
            'route' => $route,
            'middleware' => self::$this->middlewares
        ];
        self::$this->middlewares = [];
    }

    public static function run()
    {
        $response = false;
        $code = 404;
        $requestUri = self::$this->getRequestUri();

        foreach (self::$this->routes as $routeItem)
        {
            $route = $routeItem['route'];
            $middlewares = $routeItem['middleware'];
            if ($route->checkIfMatch($requestUri))
            {
                if ($route->checkMethodAllowed()) {
                    foreach ($middlewares as $middleware) {
                        $class = new $middleware;
                        $class->handle();
                    }
                    $response = $route->execute();
                    $code = 200;
                    break;
                } else {
                    $code = 405;
                }
            }
        }

        self::$this->sendResponse($response, $code);
    }

    public function sendResponse($response, $code = 200)
    {
        switch ($code) {
            case 200:
                if (is_string($response))
                {
                    echo $response;
                }
                else if (is_array($response))
                {
                    echo json_encode($response);
                }
                else if ($response instanceof Response)
                {
                    $response->execute();
                }
                break;
            case 405:
                header("HTTP/1.0 405 Method Not Allowed");
                exit('405');
                break;
            case 404:
            default:
                header("HTTP/1.0 404 Not Found");
                exit('404');
                break;

        }
    }

    public static function middleware($middleware)
    {
        if (is_array($middleware)) {
            self::$this->middlewares = $middleware;
        } else {
            self::$this->middlewares = [$middleware];
        }
        return self::$this;
    }

}
