<?php

namespace Vendor\Litpi;

class Router
{
    private $registry;
    private $path;
    private $args = array();

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function getArg($key = '')
    {
        if ($key != '') {
            if (!isset($this->args[$key])) {
                return null;
            }

            return $this->args[$key];
        } else {
            $output = '';
            //return full args string
            foreach ($this->args as $k => $v) {
                $output .= $k . '/' . $v . '/';
            }

            return $output;
        }

    }

    public function checkArg($key = '')
    {
        return isset($this->args[$key]);
    }

    public function delegate()
    {
        // Analyze route
        $this->getController($file, $module, $controller, $action, $args);

        //assign args
        $this->extractArgs($args);

        // Initiate the class
        $class = '\\controller\\' . $module . '\\' . $controller;
        $controller = new $class($this->registry);

        //refine action string : append Action
        $action .= 'Action';

        // Run action
        $controller->$action();

    }

    private function extractArgs($args)
    {
        if (count($args) == 0) {
            return false;
        }
        $this->args = $args;
    }

    private function getController(&$file, &$module, &$controller, &$action, &$args)
    {
        $route = $GLOBALS['route'];

        // Get separate parts
        $route = trim($route, '/\\');
        $parts = explode('/', $route);

        for ($i = 0; $i < count($parts); $i++) {
            $parts[$i] = $this->filterRouterInput($parts[$i]);
        }

        $module = array_shift($parts);
        $controller = array_shift($parts);
        $action = array_shift($parts);

        if (count($parts) > 0) {
            $args = $this->parseArgsString($parts);
        }
    }

    //param format: name1/value1/name2/value2
    private function parseArgsString($argArr)
    {
        $outputArr = array();

        for ($i = 0; $i < count($argArr); $i += 2) {
            if (isset($argArr[($i + 1)])) {
                $outputArr[$argArr[$i]] = strlen($argArr[($i + 1)]) == 0 ? '' : $argArr[($i + 1)];
            } else {
                $outputArr[$argArr[$i]] = '';
            }
        }

        return $outputArr;
    }

    public function filterRouterInput($input)
    {
        $output = $input;
        $output = htmlspecialchars($output);

        return $output;
    }

    public static function initRoute($defaultModule = 'site')
    {
        $route = '';

        //get the filename of the request URI online - after '?' character
        if (($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false) {
            $cleanURI = substr($_SERVER['REQUEST_URI'], 0, $pos);
        } else {
            $cleanURI = $_SERVER['REQUEST_URI'];
        }

        if (empty($_GET['route']) || $cleanURI == '/' || $cleanURI == '/index.php') {
            $route = $defaultModule;
        } else {
            $route = $_GET['route'];
        }

        return $route;
    }
}
