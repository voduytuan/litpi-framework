<?php

namespace Controller\Admin;

class Login extends BaseController
{
    public function indexAction()
    {
        $error = $warning = $formData = array();

        $redirectUrl = $_GET['redirect'];//base64 encoded

        if (isset($_POST['fsubmit'])) {
            $formData = array_merge($formData, $_POST);

            $myUser = \Model\User::getByEmail($_POST['femail']);

            if ($myUser->id > 0 && $myUser->password == \Vendor\Litpi\ViephpHashing::hash($_POST['fpassword'])) {
                session_regenerate_id(true);
                $_SESSION['userLogin'] = $myUser->id;

                //auto login
                //neu co chon chuc nang remember me
                if (isset($_POST['frememberme'])) {
                    setcookie(
                        'myHashing',
                        \Vendor\Litpi\ViephpHashing::cookiehashing($myUser->id, $_POST['fpassword']),
                        time() + 24*3600*14,
                        '/'
                    );
                } else {
                    setcookie('myHashing', "", time()-3600, '/');
                }

                ///////////////
                setcookie('islogin', '1', time() + 24 * 3600 * 14, '/');

                //tien hanh redirect
                if (strlen($redirectUrl) > 0) {
                    $redirectUrl = base64_decode($redirectUrl);
                } elseif ($_GET['returnurl'] != '') {
                    $redirectUrl = urldecode($_GET['returnurl']);
                } else {
                    $redirectUrl = $this->registry->conf['rooturl_admin'];
                }

                header('location: ' . $redirectUrl);
                exit();

            } else {
                $error[] = $this->registry->lang['controller']['errAccountInvalid'];
            }
        }

        $this->registry->smarty->assign(array(
            'formData' 	=> $formData,
            'error' 	=> $error,
            'redirectUrl' 	=> $redirectUrl,
        ));

        $this->registry->smarty->display($this->registry->smartyController.'index.tpl');
    }
}
