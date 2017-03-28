<?php
namespace App\Routing;

use Config;

class Route {
    
    public static $routes = [
        'get'       => [],
        'post'      => [],
        'patch'     => [],
        'put'       => [],
        'delete'    => [],
        'error'     => [],
    ];
    
    /**
     * Store all Directs in a array
     * @param  object $route Direct
     * @return string URI
     */
    public static function getCurrentRoute($route){
        
        Config::$route = $route;
        
        if(Config::$debug_mode){
            self::checkForMissingMethods();
        }
        
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            //CSRF token
            if(!isset($_POST['_token'])) return self::error('401', ['Missing token']);
            
            if($_POST['_token'] != $_SESSION['_token']){
               return self::error('401', ['Wrong CSRF token']);
            } 

            switch(strtoupper($_POST['_method'])) {
                    
                case 'PUT':
                    return self::method('put', $route);
                break;

                case 'PATCH':
                    return self::method('patch', $route);
                break;

                case 'DELETE':
                    return self::method('delete', $route);
                break;
              
                case 'POST':
                    return self::method('post', $route);
                break;

                default:
                    return self::error('405');
                break;
            }
        } else {
            return self::method('get', $route);
        }
    }
    
    private static function checkForMissingMethods(){
        $missing = [];
            
        foreach(self::$routes as $key => $http){
            foreach($http as $class){
                $class = explode('@', $class['callback']);
                if(!method_exists($class[0], $class[1])){
                    $missing[] = $class;
                }

            }
        }
        if(!empty($missing)){
            print_r($missing);
            die("Missing controllers");
        }
    }
    
    public static function method($method, $route){
        
        if(array_key_exists($route, self::$routes[$method])){
            $key = self::$routes[$method][$route];
            
            if(isset($key['middleware']['auth'])){
                if(!isset($_SESSION['uuid'])){
                    if(isset($key['middleware']['callback'])){
                        return call_user_func($key['middleware']['callback']);   
                    }
                    return self::error('403', 'No entry, premission denied');   
                }
            }
            return self::$routes[$method][$route];
        } else {
            return self::error('404', ['error' => 'page does not exist', 'Route' => $route, 'Method' => $method, 'post' => $_POST]);
        }
    }
    
    public static function error($error, $route = ''){
        
        return array_key_exists($error, self::$routes['error']) ? self::$routes['error'][$error] : ['error' => "$error: Please set up a $error page", 'trace' => $route];
        
        //header for right http error.
        //header("HTTP/1.0 404 Not Found");
    }
    
    public static function lists(){
        return self::$routes;
    }
}