<?php

namespace Controller\Site;

class NotFound extends BaseController
{
    public function indexAction()
    {
        header('HTTP/1.0 404 Not Found');
        readfile('./404.html');
    }
}
