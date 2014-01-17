<?php

namespace Controller\Site;

class Logout extends BaseController
{
    public function indexAction()
    {
        session_regenerate_id(true);
        session_destroy();

        setcookie('myHashing', "", time() - 3600, '/');
        setcookie('islogin', '', time() - 3600);

        header('location: ' . $this->registry->conf['rooturl_admin'] . 'login');
    }
}
