<?php

namespace Controller\Admin;

class Utility extends BaseController
{
    public function indexAction()
    {
        header('location: ' . $this->registry->conf['rooturl_admin'] . 'utility/passwordgenerator');
    }

    public function passwordGeneratorAction()
    {
        $encodedPass = '';

        if (isset($_POST['fpassword']) && strlen($_POST['fpassword']) > 0) {
            $myHasher = new \Vendor\Litpi\ViephpHashing();
            $encodedPass = $myHasher->hash($_POST['fpassword']);
        }

        $this->registry->smarty->assign(array('encodedPass' => $encodedPass));

        $contents = $this->registry->smarty->fetch($this->registry->smartyController.'passwordgenerator.tpl');
        $this->registry->smarty->assign(array(
            'menu'		=> 'passwordgenerator',
            'pageTitle'	=> 'Password Generator',
            'contents' 	=> $contents
        ));

        $this->registry->smarty->display($this->registry->smartyModule . 'index.tpl');
    }
}
