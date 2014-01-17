<?php

namespace Controller;

abstract class BaseController extends \Vendor\Litpi\Controller\BaseController
{
    protected $registry;

    abstract public function indexAction();

    public function __construct($registry)
    {
        if (SUBDOMAIN == 'm' && $registry->registry->setting['site']['enableMobileWebRedirect']) {
            $modulePre = 'm';
        } else {
            $modulePre = '';
        }

        //set smarty template container
        $registry->set('smartyControllerRoot', '_controller/');
        $registry->set('smartyModule', '_controller/' . $modulePre . $registry->module . '/');
        $registry->set('smartyController', '_controller/'.$modulePre.$registry->module.'/'.$registry->controller.'/');
        $registry->set('smartyMail', '_mail/');

        $registry->smarty->assign(array(
            'smartyControllerRoot'	=> '_controller/',
            'smartyModule'          => '_controller/' . $modulePre . $registry->module . '/',
            'smartyController'      => '_controller/' .  $modulePre . $registry->module.'/'.$registry->controller . '/',
            'smartyMail'            => '_mail/',
        ));

        $this->registry = $registry;
    }

    public function __call($name, $args)
    {
        $redirectUrl = base64_encode(\Vendor\Litpi\Helper::curPageURL());
        header('location: ' . $this->registry->conf['rooturl'] . 'notfound?r=' . $redirectUrl);
    }



    protected function getRedirectUrl()
    {
        return $this->registry->conf['rooturl'].$this->registry->module . '/'.$this->registry->controller;
    }

    protected function notfound()
    {
        $redirectUrl = base64_encode(\Vendor\Litpi\Helper::curPageURL());
        header('location: ' . $this->registry->conf['rooturl'] . 'notfound?r=' . $redirectUrl);
    }
}
