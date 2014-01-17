<?php

namespace Controller\Site;

abstract class BaseController extends \Controller\BaseController
{
    public function __construct($registry)
    {
        parent::__construct($registry);
    }

    protected function notfound()
    {
        header('HTTP/1.0 404 Not Found');
        readfile('./404.html');
        exit();
    }
}
