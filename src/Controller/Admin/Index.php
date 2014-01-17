<?php

namespace Controller\Admin;

class Index extends BaseController
{
    public function indexAction()
    {
        global $session;

        $server_php = $_SERVER['SERVER_SOFTWARE'];
        $pos = strripos($server_php, 'php');

        $formData['fserverip'] = $_SERVER['SERVER_ADDR'];
        $formData['fserver'] = trim(substr($server_php, 0, $pos-1));
        $formData['fphp'] = trim(substr($server_php, $pos));

        $this->registry->smarty->assign(array(
            'formData' => $formData,
            'stat'	=> $stat
        ));

        $contents = $this->registry->smarty->fetch($this->registry->smartyController.'index.tpl');

        $this->registry->smarty->assign(array(
            'contents' => $contents,
            'pageTitle'	=> $this->registry->lang['controller']['pageTitle_dashboard']
        ));
        $this->registry->smarty->display($this->registry->smartyModule.'index.tpl');

    }
}
