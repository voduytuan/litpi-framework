<?php

namespace Controller\Site;

class Captcha extends BaseController
{
    public function indexAction()
    {
        $width = isset($_GET['width']) ? $_GET['width'] : '120';
        $height = isset($_GET['height']) ? $_GET['height'] : '50';
        $characters = isset($_GET['characters']) && $_GET['characters'] > 1 ? $_GET['characters'] : '5';

        $captcha = new \Vendor\Other\Kcaptcha($characters, $width, $height);
        $_SESSION['verify_code'] = $captcha->getKeyString();
    }
}
