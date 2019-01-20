<?php

namespace Framework;

class Router
{
    public function route(array $static_routes, string $url) {
        // static route assignment
        $url = rtrim($url, '/').'/';
        $this->getControllerName($static_routes, $controller, $action, $paramNames, $dynamicUrl, $url);

        if($paramNames && count($paramNames) > 0){
            $params = [];
            if($dynamicUrl) {
                foreach ($paramNames as $paramName => $paramValue)
                {
                    $params[$paramName] = $paramValue;
                }
            } else {
                foreach ($paramNames as $paramName)
                {
                    $params[$paramName] = null;
                    if (isset($_POST[$paramName]))
                    {
                        $params[$paramName] = $_POST[$paramName];
                    }
                }
            }

            $controller->{$action}($params);
        } else {
            $controller->{$action}();
        }
    }

    private function getControllerName(array $routes, &$controller, &$action, &$params, &$dynamicUrl, string $url = null) {
        if (!isset($routes[$url])) {
            if($this->matchUrl($routes, $controller, $action, $params, $url)) {
                $dynamicUrl = true;
                return;
            }
            else {
                $url = null;
                $dynamicUrl = false;
            }
        }

        $controllerName = $routes[$url]['controller'];
        $action = $routes[$url]['action'];

        $keys = array_keys($routes[$url]);

        $routeParams = in_array('params', $keys) ? $routes[$url]['params'] : null;
        
        $params = $routeParams && count($routeParams) > 0 ? $routeParams : null;

        $class = "\App\Controllers\\" . $controllerName;
        $controller = new $class();
    }

    private function matchUrl(array $routes, &$controller, &$action, &$params, string $url = null){
        $urlSplited = explode("/",$url);
        $url = "";
        $id;

        foreach($urlSplited as $item){
            if(is_numeric($item)) $id = $item;
            else if($item != "") $url.= $item.'\/';
        }

        foreach($routes as $key => $value) {
            //check if match something like '/texthere/{someTextHere}'
            $pattern = '/\/'.$url.'{([^\/}]+)}/';

            if(preg_match($pattern, $key, $matches)) {
                $route = $value;
                $controllerName = $route['controller'];
                $action = $route['action'];
                $params = [$matches[1] => $id];
                
                $class = "\App\Controllers\\" . $controllerName;
                $controller = new $class();

                return true;
            }  
        }
        return false;
    }
}
