<?php

namespace Controller\Admin;

use \Vendor\Litpi\Helper as Helper;
use \Vendor\Litpi\ViephpHashing as ViephpHashing;
use \Vendor\Litpi\Uploader as Uploader;

class Profile extends BaseController
{
    public function indexAction()
    {
        if (isset($_GET['avatareditor'])) {
            $this->avatareditorAction();
            exit();
        }

        $error = array();
        $success = array();
        $contents = '';
        $formData = array();

        if ($this->registry->me->id > 0) {

            $myUser = new \Model\User($this->registry->me->id);

            $formData['ffullname'] = $myUser->fullname;
            $formData['femail'] = $myUser->email;
            $formData['fphone'] = $myUser->phone;
            $formData['faddress'] = $myUser->address;
            $formData['fregion'] = $myUser->region;
            $formData['fcountry'] = $myUser->country;
            $formData['fbio'] = $myUser->bio;
            $formData['fgender'] = $myUser->gender;
            $formData['fwebsite'] = $myUser->website;

            if ($myUser->birthday != '0000-00-00') {
                $bdTmp = date_parse($myUser->birthday . ' 00:00:01');
                $formData['fbirthday'] = $bdTmp['day'] . '/' . $bdTmp['month'] . '/' . $bdTmp['year'];
            }

            if (isset($_GET['deleteavatar'])
                && $_SESSION['avatarDeleteToken'] == $_GET['deleteavatar'] && $myUser->avatar != '') {
                $myUser->deleteImage();
                $myUser->updateAvatar();
            }

            if (!empty($_POST['fsubmit'])) {
                $formData = array_merge($formData, $_POST);
                $formData = Helper::arrayStriptags($formData);

                if ($this->submitAccountValidator($formData, $error)) {

                    $myUser->avatarCurrent = $myUser->avatar;
                    $myUser->phone = strip_tags($formData['fphone']);
                    $myUser->address = strip_tags($formData['faddress']);
                    $myUser->bio = strip_tags($formData['fbio']);
                    $myUser->birthday = strip_tags($formData['fbirthday']);
                    $myUser->website = strip_tags($formData['fwebsite']);

                    if (strlen($formData['fbirthday']) > 0) {
                        $tmp = explode('/', $formData['fbirthday']);
                        $myUser->birthday = $tmp[2] . '-' . ((int) $tmp[1] < 10 ? '0'.(int) $tmp[1] : (int) $tmp[1])
                            . '-' . ((int) $tmp[0] < 10 ? '0'.(int) $tmp[0] : (int) $tmp[0]);
                    } else {
                        $myUser->birthday = '0000-00-00';
                    }

                    if (isset($this->registry->setting['region'][$formData['fregion']])) {
                        $formData['fregion'] = (int) $formData['fregion'];
                    } else {
                        $formData['fregion'] = 0;
                    }

                    if ($myUser->updateData(array(
                        'fullname' => Helper::plaintext($formData['ffullname']),
                        'gender' => (int) $formData['fgender'],
                        'region' => (int) $formData['fregion']
                        ), $error) > 0) {

                        $success[] = $this->registry->lang['controller']['succUpdate'];

                        //update Date last action
                        $myUser->updateDateLastaction();

                        //clear cache for this user
                        \Model\User::cacheDelete($myUser->id);
                    } else {
                        $error[] = $this->registry->lang['controller']['errUpdate'];
                    }
                }
            }

            $_SESSION['avatarDeleteToken'] = Helper::getSecurityToken();

            $this->registry->smarty->assign(array(
                'formData'  => $formData,
                'user'      => $myUser,
                'error'     => $error,
                'success'   => $success,
            ));
            $contents = $this->registry->smarty->fetch($this->registry->smartyController.'index.tpl');

            $this->registry->smarty->assign(array(
                'contents' => $contents,
                'pageTitle' => $this->registry->lang['controller']['pageTitle'],
                'pageKeyword' => $this->registry->lang['controller']['pageKeyword'],
                'pageDescription' => $this->registry->lang['controller']['pageDescription'],
                ));
            $this->registry->smarty->display($this->registry->smartyModule.'index.tpl');
        } else {
            $this->notfound();
        }

    }

    /**
     * Used for change password (of native account, not OAUTH account)
     */
    public function changepasswordAction()
    {
        $error = array();
        $success = array();
        $warning = array();
        $contents = '';
        $formData = array();

        if ($this->registry->me->id > 0) {
            $formData['femail'] = $this->registry->me->email;

            if (!empty($_POST['fsubmitpassword']) && $this->registry->me->canChangePassword()) {
                //validate register data
                $formData = array_merge($formData, $_POST);

                if ($this->submitPasswordValidator($formData, $error)) {
                    $this->registry->me->newpass = $formData['fnewpass1'];

                    if ($this->registry->me->updateData(array(), $error) > 0) {
                        $success[] = $this->registry->lang['controller']['succUpdate'];
                    } else {
                        $error[] = $this->registry->lang['controller']['errUpdate'];
                    }
                }
            }

            //Password empty means This user login from google, facebook...
            if (!$this->registry->me->canChangePassword()) {
                $warning[] = $this->registry->lang['controller']['warnPasswordNotSetForOAuthLogin'];

                if (!empty($_POST['fsubmitsetpassword'])) {
                    //validate register data
                    $formData = array_merge($formData, $_POST);

                    if ($this->submitSetPasswordValidator($formData, $error)) {
                        $this->registry->me->newpass = $formData['fnewpass1'];

                        if ($this->registry->me->updateData(array(), $error) > 0) {
                            $success[] = $this->registry->lang['controller']['succUpdate'];
                        } else {
                            $error[] = $this->registry->lang['controller']['errUpdate'];
                        }
                    }
                }
            }

            $this->registry->smarty->assign(array(  'formData'  => $formData,
                                                    'user'      => $this->registry->me,
                                                    'error'     => $error,
                                                    'success'   => $success,
                                                    'warning'   => $warning,
                                                    ));
            $contents = $this->registry->smarty->fetch($this->registry->smartyController.'changepassword.tpl');

            $this->registry->smarty->assign(array('contents' => $contents,
                                            'pageTitle' => $this->registry->lang['controller']['pageTitle'],
                                            'pageKeyword' => $this->registry->lang['controller']['pageKeyword'],
                                            'pageDescription' => $this->registry->lang['controller']['pageDescription'],
                                            ));
            $this->registry->smarty->display($this->registry->smartyModule.'index.tpl');
        } else {
            header('location: ' . $this->registry->conf['rooturl'] . 'notfound');
            exit();
        }

    }

    public function avataruploadAction()
    {
        if ($this->registry->me->avatar != '') {
            //redirect to edit photo
            header('location:' . $this->registry->conf['rooturl_admin'] . 'profile/avatareditor');
        }

        $error = array();

        if (isset($_POST['fsubmit'])) {
            //accepted file size
            $maxFileSize = $this->registry->setting['avatar']['imageMaxSize'];
            $validMimetype = array('image/gif', 'image/jpeg', 'image/png');

            if ($_FILES['fimage']['size'] < $maxFileSize && in_array($_FILES['fimage']['type'], $validMimetype)) {
                $curDateDir = Helper::getCurrentDateDirName();  //path format: ../2009/September/
                $extPart = substr(strrchr($_FILES['fimage']['name'], '.'), 1);
                $namePart = Helper::codau2khongdau(
                    $this->registry->me->fullname.'-'.$this->registry->me->id,
                    true,
                    true
                ) . '-' . time();
                $name = $namePart . '.' . $extPart;

                $uploader = new Uploader(
                    $_FILES['fimage']['tmp_name'],
                    $name,
                    $this->registry->setting['avatar']['imageDirectory'] . $curDateDir
                );

                $uploadError = $uploader->upload(false, $name);

                if ($uploadError != Uploader::ERROR_UPLOAD_OK) {
                    switch ($uploadError) {
                        case Uploader::ERROR_FILESIZE:
                            $error[] = $this->registry->lang['global']['errUploadFileSize'];
                            break;
                        case Uploader::ERROR_FILETYPE:
                            $error[] = $this->registry->lang['global']['errUploadFileType'];
                            break;
                        case Uploader::ERROR_PERMISSION:
                            $error[] = $this->registry->lang['global']['errUploadFilePermission'];
                            break;
                        default:
                            $error[] = $this->registry->lang['global']['errUploadFileUnknown'];
                            break;
                    }
                } else {
                    //update database
                    $this->registry->me->avatar = $curDateDir . $name;
                    $this->registry->me->updateAvatar();
                    $this->registry->me->postProcessingAvatar($curDateDir, $name);

                    //redirect to crop page
                    header('location: ' . $this->registry->conf['rooturl_admin'] . 'profile/avatareditor');
                }
            } else {
                $error[] = $this->registry->lang['controller']['errAvatarInvalid'];
            }
        }

        $this->registry->smarty->assign(array(  'formData'  => $formData,
                                                'error'     => $error,
                                                ));
        $this->registry->smarty->display($this->registry->smartyController.'avatarupload.tpl');
    }

    public function avatareditorAction()
    {
        if ($this->registry->me->avatar == '') {
            //redirect to upload photo
            header('location:' . $this->registry->conf['rooturl_admin'] . 'profile/avatarupload');
        } else {
            $error = array();
            $name = $this->registry->me->avatar;
            $fullImagePath = $imagefile = $endImage = $this->registry->setting['avatar']['imageDirectory'] . $name;
            list($imagewidth, $imageheight, $imageType) = getimagesize($imagefile);

            if (isset($_POST['fsavethumbnail'])) {
                $x1 = intval($_POST["x1"]);
                $y1 = intval($_POST["y1"]);
                $x2 = intval($_POST["x2"]);
                $y2 = intval($_POST["y2"]);
                $w = intval($_POST["w"]);
                $h = intval($_POST["h"]);

                list($width, $height, $type, $attr) = @getimagesize($imagefile);
                if ($w > $width || $h > $height) {
                    $error[] = 'Image size is not correct.';
                } else {
                    //Crop and apply to Medium Image
                    $croppedImage = new \Vendor\Litpi\ImageCropper(
                        $this->registry->setting['avatar']['imageDirectory'] . $this->registry->me->mediumImage(),
                        $imagefile,
                        $w,
                        $h,
                        $x1,
                        $y1,
                        1
                    );
                    if ($croppedImage->cropPass) {
                        //Resize Medium & apply to thumb image
                        $this->registry->me->postCroppingAvatar();

                        //clear cache for this user
                        \Model\User::cacheDelete($this->registry->me->id);

                        $this->registry->smarty->display($this->registry->smartyController.'avatareditor_success.tpl');
                        exit();
                    } else {
                        $error[] = 'Error while cropping your image. Please try again.';
                    }
                }
            }

            $this->registry->smarty->assign(array(  'name'  => $name,
                                                    'fullImagePath' => $fullImagePath,
                                                    'imagewidth' => $imagewidth,
                                                    'imageheight' => $imageheight,
                                                    'error'     => $error,
                                                    ));
            $this->registry->smarty->display($this->registry->smartyController.'avatareditor.tpl');
        }
    }

    private function submitAccountValidator($formData, &$error)
    {
        $pass = true;

        //check password length
        if (strlen($formData['ffullname']) < 6) {
            $error[] =  $this->registry->lang['controller']['errFullnameRequired'];
            $pass = false;
        }

        if ($formData['fgender'] != ''
            && $formData['fgender'] != \Model\User::GENDER_MALE
            && $formData['fgender'] != \Model\User::GENDER_FEMALE) {
            $error[] = $this->registry->lang['controller']['errGenderInvalid'];
            $pass = false;
        }

        //check if input birthday
        if (strlen($formData['fbirthday']) > 0) {
            if (!preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $formData['fbirthday'], $match)) {
                $error[] = $this->registry->lang['controller']['errBirthdayFormat'];
                $pass = false;
            } else {
                if (!checkdate($match[2], $match[1], $match[3])
                    || mktime(0, 0, 1, $match[2], $match[1], $match[3]) > time()) {
                    $error[] = $this->registry->lang['controller']['errBirthdayFormat'];
                    $pass = false;
                }
            }
        }

        return $pass;
    }

    private function submitPasswordValidator($formData, &$error)
    {
        $pass = true;

        //check oldpass
        //change password
        if (!viephpHashing::authenticate($formData['foldpass'], $this->registry->me->password)
            && $this->registry->me->password != '') {
            $pass = false;
            $this->registry->me->newpass = '';
            $error[] = $this->registry->lang['controller']['errOldpassNotvalid'];
        }

        if (strlen($formData['fnewpass1']) < 6) {
            $pass = false;
            $this->registry->me->newpass = '';
            $error[] = $this->registry->lang['controller']['errNewpassnotvalid'];
        }

        if ($formData['fnewpass1'] != $formData['fnewpass2']) {
            $pass = false;
            $this->registry->me->newpass = '';
            $error[] = $this->registry->lang['controller']['errNewpassnotmatch'];
        }

        return $pass;
    }

    private function submitSetPasswordValidator($formData, &$error)
    {
        $pass = true;

        if ($this->registry->me->email == '') {
            $pass = false;
            $this->registry->me->newpass = '';
            $error[] = $this->registry->lang['controller']['errEmailRequireForSetPassword'];
        }

        if (strlen($formData['fnewpass1']) < 6) {
            $pass = false;
            $this->registry->me->newpass = '';
            $error[] = $this->registry->lang['controller']['errNewpassnotvalid'];
        }

        if ($formData['fnewpass1'] != $formData['fnewpass2']) {
            $pass = false;
            $this->registry->me->newpass = '';
            $error[] = $this->registry->lang['controller']['errNewpassnotmatch'];
        }

        return $pass;
    }
}
