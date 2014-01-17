<?php

namespace Controller\Admin;

use \Vendor\Litpi\Helper as Helper;

class User extends BaseController
{
    public $recordPerPage = 50;

    public function indexAction()
    {
        $formData       = array();

        if (isset($_GET['page']) && $_GET['page'] > 1) {
            $formData['page'] = (int) $_GET['page'];
        }

        if (isset($_GET['sortby']) && $_GET['sortby'] != '') {
            $formData['sortby'] = $_GET['sortby'];
        }

        if (isset($_GET['sorttype']) && $_GET['sorttype'] != '') {
            $formData['sorttype'] = $_GET['sorttype'];
        }

        if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
            $formData['fkeyword'] = $_GET['keyword'];
        }

        $formData['filtertaglist'] = array();
        $filterNameList = array('email', 'groupid', 'region', 'gender', 'id');
        foreach ($filterNameList as $filter) {
            if (isset($_GET[$filter]) && $_GET[$filter] != '') {
                $formData['filtertaglist'][] = array(
                    'name' => $filter,
                    'namelabel' => $this->registry->lang['controller']['label'.ucfirst($filter)],
                    'value' => Helper::plaintext($_GET[$filter])
                );
            }
        }

        $this->registry->smarty->assign(array(
            'formData'      => $formData,
            'userGroups'=> \Model\User::getGroupnameList(),
        ));


        $contents = $this->registry->smarty->fetch($this->registry->smartyController.'index.tpl');

        $this->registry->smarty->assign(array(
            'pageTitle' => $this->registry->lang['controller']['pageTitle_list'],
            'contents'  => $contents
        ));

        $this->registry->smarty->display($this->registry->smartyModule . 'index.tpl');
    }

    public function jsondataAction()
    {
        $formData       = array();
        $_SESSION['securityToken'] = Helper::getSecurityToken();//Token
        $page           = (int)($_POST['page']) > 0 ? (int)($_POST['page']) : 1;


        $emailFilter = (string) ($_POST['email']);
        $groupidFilter = (int) ($_POST['groupid']);
        $regionFilter = (int) ($_POST['region']);
        $genderFilter = (int) ($_POST['gender']);
        $idFilter = (int) ($_POST['id']);

        $keywordFilter = Helper::plaintext($_POST['keyword']);
        $searchKeywordIn = (string) $_POST['searchin'];

        //check sort column condition
        $sortby     = $_POST['sortby'];
        if ($sortby == '') {
            $sortby = 'id';
        }
        $formData['sortby'] = $sortby;

        $sorttype   = $_POST['sorttype'];
        if (strtoupper($sorttype) != 'ASC') {
            $sorttype = 'DESC';
        }
        $formData['sorttype'] = $sorttype;



        if ($emailFilter != "") {
            $formData['femail'] = $emailFilter;
        }

        if ($groupidFilter > 0) {
            $formData['fgroupid'] = $groupidFilter;
        }

        if ($regionFilter > 0) {
            $formData['fregion'] = $regionFilter;
        }

        if ($genderFilter > 0) {
            $formData['fgender'] = $genderFilter;
        }

        if ($idFilter > 0) {
            $formData['fid'] = $idFilter;
        }

        if (strlen($keywordFilter) > 0) {

            if ($searchKeywordIn == 'screenname') {
            } elseif ($searchKeywordIn == 'fullname') {
            }
            $formData['fkeyword'] = $formData['fkeywordFilter'] = $keywordFilter;
            $formData['fsearchin'] = $formData['fsearchKeywordIn'] = $searchKeywordIn;
        }

        //tim tong so
        $total = \Model\User::getUsers($formData, $sortby, $sorttype, 0, true);

        //get latest account
        $Users = \Model\User::getUsers(
            $formData,
            $sortby,
            $sorttype,
            (($page - 1) * $this->recordPerPage).','.$this->recordPerPage
        );

        $jsondata = array();
        $jsondata['total'] = (int) $total;
        $jsondata['totalpage'] = (int) ceil($total / $this->recordPerPage);
        $jsondata['page'] = (int) $page;
        $jsondata['token'] = (string) $_SESSION['securityToken'];
        $jsondata['sortby'] = (string) $sortby;
        $jsondata['sorttype'] = (string) $sorttype;
        $jsondata['primaryproperty'] = 'id';
        $jsondata['editurlprefix'] = $this->registry->conf['rooturl_admin']
            . $this->registry->controller.'/edit/id/';
        $jsondata['deleteurlprefix'] = $this->registry->conf['rooturl_admin']
            . $this->registry->controller.'/delete/id/';

        $jsondata['items'] = array();

        foreach ($Users as $myUser) {
            $jsondata['items'][] = array(
                'id' => (int) $myUser->id,
                'email' => (string) $myUser->email,
                'fullname' => (string) $myUser->fullname,
                'groupid' => (string) $myUser->getGroupName(),
                'gender' => (string) $myUser->getGenderText(),
                'region' => (string) $myUser->getRegionName(),
                'datecreated' => date('d/m/Y', $myUser->datecreated),
                'datelastaction' => date('H:i:s, d/m/Y', $myUser->datelastaction),
            );
        }

        header('Content-type: text/json');
        echo json_encode($jsondata);

    }


    public function deleteAction()
    {
        $success = 0;
        $message = '';

        $id = (int)$this->registry->router->getArg('id');
        $myUser = new \Model\User($id);
        if ($myUser->id > 0 && Helper::checkSecurityToken()) {

            if ($myUser->id == 1) {
                $message = 'Can not delete Root User (User ID #1)';
            } else {
                if ($myUser->delete()) {
                    $success = 1;
                    $message = str_replace('###id###', $myUser->id, $this->registry->lang['controller']['succDelete']);
                } else {
                    $message = str_replace('###id###', $myUser->id, $this->registry->lang['controller']['errDelete']);
                }
            }
        } else {
            $message = $this->registry->lang['controller']['errNotFound'];
        }

        header("content-type: text/xml");
        echo '<?xml version="1.0" encoding="utf-8"?><result><success>'
            . $success.'</success><message>'.$message.'</message></result>';

    }

    public function bulkapplyAction()
    {
        $success = 0;
        $message = '';
        $moremessage = '';

        //Extract & Refine ID List
        $idListTmp = explode(',', $_POST['ids']);
        $idList = array();
        foreach ($idListTmp as $id) {
            $id = trim($id);
            if (is_numeric($id) && !in_array($id, $idList)) {
                $idList[] = $id;
            }
        }

        $bulkaction = $_POST['bulkaction'];
        if ($bulkaction != '' && count($idList) > 0 && Helper::checkSecurityToken()) {
            //check for delete
            if ($bulkaction == 'delete') {
                $delArr = $idList;
                $deletedItems = array();
                $cannotDeletedItems = array();
                foreach ($delArr as $id) {
                    $myUser = new \Model\User($id);

                    if ($myUser->id > 0 && $myUser->id != 1) {
                        //tien hanh xoa
                        if ($myUser->delete()) {
                            $deletedItems[] = $myUser->id;
                        } else {
                            $cannotDeletedItems[] = $myUser->id;
                        }
                    } else {
                        $cannotDeletedItems[] = $myUser->id;
                    }
                }

                if (count($deletedItems) > 0) {
                    $success = 1;

                    $moremessage .= '<successlist>';
                    foreach ($deletedItems as $id) {
                        $moremessage .= '<successitem>'.$id.'</successitem>';
                    }
                    $moremessage .= '</successlist>';
                }

                if (count($cannotDeletedItems) > 0) {
                    $moremessage .= '<faillist>';
                    foreach ($deletedItems as $id) {
                        $moremessage .= '<failitem>'.$id.'</failitem>';
                    }
                    $moremessage .= '</faillist>';
                }
            } else {
                //bulk action not select, show error
                $message = $this->registry->lang['default']['bulkActionInvalidWarn'];
            }
        } else {
            $message = $this->registry->lang['controller']['errNotFound'];
        }


        header("content-type: text/xml");
        echo '<?xml version="1.0" encoding="utf-8"?><result><success>'
            . $success.'</success><message>'.$message.'</message>'.$moremessage.'</result>';

    }

    public function addAction()
    {
        $error     = array();
        $success     = array();
        $contents     = '';
        $formData     = array();

        if (!empty($_POST['fsubmit'])) {
            if ($_SESSION['userAddToken']==$_POST['ftoken']) {//kiem tra token
                $formData = array_merge($formData, $_POST);

                if ($this->addActionValidator($formData, $error)) {//kiem tra du lieu nhap
                    $myUser = new \Model\User();
                    $myUser->groupid = (int) $formData['fgroupid'];
                    $myUser->email = Helper::plaintext($formData['femail']);
                    $myUser->password = \Vendor\Litpi\ViephpHashing::hash($formData['fpassword']);
                    $myUser->fullname = Helper::plaintext($formData['ffullname']);

                    if ($myUser->addData() > 0) {
                        $success[] = str_replace(
                            '###email###',
                            $myUser->email,
                            $this->registry->lang['controller']['succAdd']
                        );
                        $formData = array('fgroupid' => $formData['fgroupid']);
                    } else {
                        $error[] = $this->registry->lang['controller']['errAdd'];
                    }
                }
            }
        }

        $_SESSION['userAddToken'] = Helper::getSecurityToken();  //them token moi

        $this->registry->smarty->assign(array(
            'formData'         => $formData,
            'redirectUrl'    => $this->getRedirectUrl(),
            'userGroups'    => \Model\User::getGroupnameList(),
            'error'            => $error,
            'success'        => $success
        ));
        $contents = $this->registry->smarty->fetch($this->registry->smartyController.'add.tpl');

        $this->registry->smarty->assign(array(
            'menu'        => 'useradd',
            'pageTitle'    => $this->registry->lang['controller']['pageTitle_add'],
            'contents'     => $contents
        ));

        $this->registry->smarty->display($this->registry->smartyModule . 'index.tpl');
    }

    public function editAction()
    {
        $id = (int) $this->registry->router->getArg('id');
        $myUser = new \Model\User($id);
        $redirectUrl = $this->getRedirectUrl();
        if ($myUser->id > 0) {
            //check priviledge priority
            //Yeu cau de edit:
            // 1. Hoac la admin
            if ($this->registry->me->groupid == GROUPID_ADMIN || $this->registry->me->groupid == GROUPID_MODERATOR) {
                $error         = array();
                $success     = array();
                $contents     = '';
                $formData     = array();

                $formData['fgroupid'] = $myUser->groupid;
                $formData['femail'] = $myUser->email;
                $formData['ffullname'] = $myUser->fullname;
                $formData['fgender'] = $myUser->gender;
                $formData['fbirthday'] = $myUser->birthday;
                $formData['fphone'] = $myUser->phone;
                $formData['faddress'] = $myUser->address;
                $formData['fregion'] = $myUser->region;
                $formData['fcountry'] = $myUser->country;
                $formData['fwebsite'] = $myUser->website;
                $formData['fbio'] = $myUser->bio;
                $formData['foauthpartner'] = $myUser->oauthPartner;
                $formData['foauthuid'] = $myUser->oauthUid;

                if (!empty($_POST['fsubmit'])) {
                    if ($_SESSION['userEditToken']==$_POST['ftoken']) {
                        $formData = array_merge($formData, $_POST);

                        if ($this->editActionValidator($formData, $error)) {//kiem tra du lieu nhap
                            $myUser->birthday = Helper::plaintext($formData['fbirthday']);
                            $myUser->phone = Helper::plaintext($formData['fphone']);
                            $myUser->address = Helper::plaintext($formData['faddress']);
                            $myUser->country = Helper::plaintext($formData['fcountry']);
                            $myUser->website = Helper::plaintext($formData['fwebsite']);
                            $myUser->bio = Helper::plaintext($formData['fbio']);
                            $myUser->oauthPartner = (int) $formData['foauthpartner'];
                            $myUser->oauthUid = Helper::plaintext($formData['foauthuid']);

                            if (isset($_POST['fdeleteimage']) && $_POST['fdeleteimage'] == '1') {
                                $myUser->deleteImage();
                            }

                            if ($myUser->updateData(array(
                                'fullname' => Helper::plaintext($formData['ffullname']),
                                'groupid' => (int) $formData['fgroupid'],
                                'region' => (int) $formData['fregion'],
                                'gender' => (int) $formData['fgender'],

                                ))) {
                                $success[] = str_replace(
                                    '###email###',
                                    $myUser->email,
                                    $this->registry->lang['controller']['succUpdate']
                                );

                            } else {
                                $error[] = $this->registry->lang['controller']['errUpdate'];
                            }
                        }
                    }
                }

                $_SESSION['userEditToken']=Helper::getSecurityToken();//Tao token moi
                $this->registry->smarty->assign(array(
                    'formData'     => $formData,
                    'myUser'	=> $myUser,
                    'redirectUrl'=> $redirectUrl,
                    'encoderedirectUrl'=> base64_encode($redirectUrl),
                    'userGroups'	=> \Model\User::getGroupnameList(),
                    'error'        => $error,
                    'success'    => $success
                ));
                $contents .= $this->registry->smarty->fetch($this->registry->smartyController.'edit.tpl');
                $this->registry->smarty->assign(array(
                    'menu'        => 'userlist',
                    'pageTitle'    => $this->registry->lang['controller']['pageTitle_edit'],
                    'contents'             => $contents
                ));
                $this->registry->smarty->display($this->registry->smartyModule . 'index.tpl');
            } else {
                $this->notfound();
            }

        } else {
            $this->notfound();
        }
    }

    public function resetpassAction()
    {

        $success = 0;
        $message = '';

        $id = (int) $this->registry->router->getArg('id');
        $myUser = new \Model\User($id);
        $redirectUrl = $this->getRedirectUrl();

        if ($myUser->id > 0) {
            //check priviledge priority
            //Yeu cau de edit:
            // 1. Hoac la admin
            // 2. Hoac la edit ban than, dung cho moderator, judge...
            // 3. Hoac la co priority number < priority number cua user duoc edit
            if ($this->registry->me->groupid == GROUPID_ADMIN || ($this->registry->me->id == $myUser->id)) {
                $error 		= array();
                $success 	= array();
                $contents 	= '';
                $formData 	= array();

                srand((double) microtime()*1000000);
                $newpass = rand(100000, 999999);

                if ($myUser->resetpass($newpass)) {

                    $success = 1;

                    //send mail
                    $this->registry->smarty->assign(array(
                        'newpass' => $newpass,
                        'myUser'	=> $myUser
                    ));
                    $mailContents = $this->registry->smarty->fetch($this->registry->smartyMail.'user/resetpass.tpl');
                    $sender=  new \Vendor\Litpi\SendMail(
                        $this->registry,
                        $myUser->email,
                        $myUser->fullname,
                        str_replace(
                            '{USERNAME}',
                            $myUser->username,
                            $this->registry->setting['mail']['subjectAdminResetpassUser']
                        ),
                        $mailContents,
                        $this->registry->setting['mail']['fromEmail'],
                        $this->registry->setting['mail']['fromName']
                    );

                    if ($sender->send()) {
                        $message = str_replace(
                            '###email###',
                            $myUser->email,
                            $this->registry->lang['controller']['succResetpass']
                        ) . ' (New password: '.$newpass.')';
                    } else {
                        $message = str_replace(
                            '###email###',
                            $myUser->email,
                            $this->registry->lang['controller']['errResetpassSendMail']
                        ) . ' (New password: '.$newpass.')';
                    }
                } else {
                    $message = $this->registry->lang['controller']['errResetpass'];
                }

            } else {
                $message = $this->registry->lang['global']['notpermissiontitle'];
            }

        } else {
            $message = $this->registry->lang['controller']['errNotFound'];
        }

        header("content-type: text/xml");
        echo '<?xml version="1.0" encoding="utf-8"?><result><success>'
            . $success.'</success><message>'.$message.'</message></result>';

    }

    ####################################################################################################
    ####################################################################################################
    ####################################################################################################

    private function addActionValidator($formData, &$error)
    {
        $pass = true;

        if ($formData['fgroupid'] == 0) {
            $error[] = $this->registry->lang['controller']['errGroupInvalid'];
            $pass = false;
        }

        //kiem tra email co dung dinh dang hay khong    :validateEmail
        if (!Helper::validateEmail($formData['femail'])) {
            $error[] = $this->registry->lang['controller']['errEmailInvalid'];
            $pass = false;
        } else {
            //kiem tra co trung email hay khong
            if (\Model\User::getByEmail($formData['femail'])->id > 0) {
                $error[] = $this->registry->lang['controller']['errEmailExisted'];
                $pass = false;
            }
        }

        //kiem tra password
        if ($formData['fpassword'] == '') {
            $error[] = $this->registry->lang['controller']['errPasswordRequired'];
            $pass = false;
        } elseif ($formData['fpassword']!=$formData['fpassword2']) {//nhap lai password khong dung nhu password dau
            $error[] = $this->registry->lang['controller']['errPasswordRetype'];
            $pass = false;
        }

        if ($formData['ffullname'] == '') {
            $error[] = $this->registry->lang['controller']['errFullnameRequired'];
            $pass = false;
        }

        return $pass;
    }

    //khong cap nhat username
    private function editActionValidator($formData, &$error)
    {
        $pass = true;

        if ($formData['fgroupid'] == 0) {
            $error[] = $this->registry->lang['controller']['errGroupRequired'];
            $pass = false;
        }

        if ($formData['ffullname'] == '') {
            $error[] = $this->registry->lang['controller']['errFullnameRequired'];
            $pass = false;
        }

        if ($formData['fgender'] > 0
            && $formData['fgender'] != \Model\User::GENDER_MALE
            && $formData['fgender'] != \Model\User::GENDER_FEMALE) {
            $error[] = $this->registry->lang['controller']['errGenderInvalid'];
            $pass = false;
        }

        return $pass;
    }
}
