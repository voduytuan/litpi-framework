<?php

namespace Controller\Admin;

class ForgotPass extends BaseController
{

    public function indexAction()
    {
        //Extracting RedirectURL
        $redirectUrl = $_GET['redirect'];//base64 encoded
        if (strlen($redirectUrl) > 0) {
            $redirectUrl = base64_decode($redirectUrl);
        } elseif ($_GET['returnurl'] != '') {
            $redirectUrl = urldecode($_GET['returnurl']);
        } else {
            $redirectUrl = $this->registry->conf['rooturl'];
        }

        $error = $warning = $formData = $success = array();
        if (isset($_POST['fsubmit'])) {
            $formData = $_POST;

            if ($this->submitValidate($formData, $error)) {
                $myUser = \Model\User::getByEmail($formData['femail']);

                if ($myUser->id > 0) {

                    //xu ly de tai activatedcode cho viec change password
                    $activatedCode = md5($myUser->id . $myUser->email . rand(1000, 9999) . time() . \Vendor\Litpi\ViephpHashing::$secretString);
                    $myUser->activatedcode = $activatedCode;

                    if ($myUser->updateData(array(), $error)) {
                        $_SESSION['forgotpassSpam'] = time();

                        //tien hanh goi email
                        //////////////////////////////////////////////////////////////////////////////////////////////////
                        //////////////////////////////////////////////////////////////////////////////////////////////////
                        //send mail to user
                        $this->registry->smarty->assign(array(
                            'activatedCode' 	=> $activatedCode,
                            'myUser'	=> $myUser
                        ));
                        $mailContents = $this->registry->smarty->fetch($this->registry->smartyMail.'forgotpass/user.tpl');
                        $sender =  new SendMail(
                            $this->registry,
                            $myUser->email,
                            $myUser->fullname,
                            'Reset Password Information from ' .$this->registry->conf['host'],
                            $mailContents,
                            $this->registry->setting['mail']['fromEmail'],
                            $this->registry->setting['mail']['fromName']
                        );

                        if ($sender->send()) {
                            $success[] = 'Vui lòng kiểm tra email để tiếp tục tìm lại mật khẩu';
                        } else {
                            $error[] = 'Gửi mail thất bại vui lòng thử lại';
                        }

                    }//end updateData()
                }

            }

        }//end submit

        $_SESSION['forgotpassToken'] = \Vendor\Litpi\Helper::getSecurityToken();

        $this->registry->smarty->assign(array(
            'formData' => $formData,
            'error' => $error,
            'success' => $success,
            'warning' => $warning,
            'redirectUrl' => $redirectUrl,
            'redirectUrlEncode' => base64_encode($redirectUrl)
        ));

        $this->registry->smarty->display($this->registry->smartyController . 'index.tpl');

    }

    public function resetAction()
    {
        //Extracting RedirectURL
        $redirectUrl = $_GET['redirect'];//base64 encoded
        if (strlen($redirectUrl) > 0) {
            $redirectUrl = base64_decode($redirectUrl);
        } elseif ($_GET['returnurl'] != '') {
            $redirectUrl = urldecode($_GET['returnurl']);
        } else {
            $redirectUrl = $this->registry->conf['rooturl'];
        }

        $error = $warning = $formData = array();
        $email = (string) $_GET['email'];
        $activatedCode = (string) $_GET['code'];

        //Found user by email
        $myUser = \Model\User::getByEmail(urldecode($email));

        if ($myUser->id > 0) {
            if ($myUser->activatedcode != $activatedCode && false) {
                $this->notfound();
            } else {
                if (isset($_POST['fsubmit'])) {
                    $formData = $_POST;

                    if ($formData['fpassword'] != $formData['fpassword2']) {
                        $error[] = 'Mật khẩu và Mật khẩu xác nhận không giống nhau.';
                    } else {
                        if (strlen($formData['fpassword']) < 6) {
                            $error[] = 'Mật khẩu tối thiểu phải có 6 kí tự';
                        } else {
                            $myUser->newpass = $_POST['fpassword'];
                            $myUser->activatedcode = '';

                            if ($myUser->updateData()) {
                                $success[] = 'Mật khẩu được thay đổi thành công';

                                header('location: ' . $this->registry->conf['rooturl']. 'login?from=forgotpass&email='.$myUser->email.'&redirect=' . base64_encode($redirectUrl));
                                exit();
                            } else {
                                $error[] = 'Có lỗi khi cập nhật mật khẩu mới. Hãy thử lại.';
                            }
                        }
                    } //end validate form
                } //end submit

                $this->registry->smarty->assign(array(
                    'formData' => $formData,
                    'myUser' => $myUser,
                    'error' => $error,
                    'warning' => $warning,
                    'redirectUrlEncode' => base64_encode($redirectUrl),
                ));

                $this->registry->smarty->display($this->registry->smartyController . 'reset.tpl');
            }
        } else {
            $this->notfound();
        }
    }

    protected function submitValidate($formData, &$error)
    {
        $pass = true;
        //check form token
        if ($formData['ftoken'] != $_SESSION['forgotpassToken']) {
            $pass = false;
            $error[] = $this->registry->lang['default']['securityTokenInvalid'];
        }

        //check spam
        $forgotpassExpire = 10; //seconds
        if (isset($_SESSION['forgotpassSpam']) && time() - $_SESSION['forgotpassSpam'] < $forgotpassExpire) {
            $error[] = $this->registry->lang['controller']['errSpam'];
            $pass = false;
        }

        //check email length
        if (!\Vendor\Litpi\Helper::validateEmail($formData['femail'])) {
            $error[] = $this->registry->lang['controller']['errInvalidEmail'];
            $pass = false;
        } else {
            $myUser = \Model\User::getUsers(array('femail' => $formData['femail']));
            if ($myUser[0]->id == 0) {
                $error[] = $this->registry->lang['controller']['errAccountInvalid'];
                $pass = false;
            }
        }

        return $pass;
    }
}
