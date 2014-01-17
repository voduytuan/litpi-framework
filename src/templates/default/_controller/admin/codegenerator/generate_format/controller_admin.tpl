<?php

namespace Controller\{{CONTROLLER_NAMESPACE}};

use \Vendor\Litpi\Helper as Helper;

class {{CONTROLLER_CLASS}} extends BaseController
{
    private $recordPerPage = {{CONTROLLER_RECORDPERPAGE}};

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
        $filterNameList = array({{FILTERABLE_ARRAY}});
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
            {{CONSTANT_CONTROLLER_ASSIGN}}
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

        {{FILTERABLE_GET_ARGUMENTS}}
        {{SEARCHABLETEXT_GET_ARGUMENTS}}

        //check sort column condition
        $sortby     = $_POST['sortby'];
        if ($sortby == '') {
            $sortby = '{{PRIMARY_PROPERTY}}';
        }
        $formData['sortby'] = $sortby;

        $sorttype   = $_POST['sorttype'];
        if (strtoupper($sorttype) != 'ASC') {
            $sorttype = 'DESC';
        }
        $formData['sorttype'] = $sorttype;

        {{FILTERABLE_APPLY_FORMDATA}}
        {{SEARCHABLETEXT_APPLY_FORMDATA}}

        //tim tong so
        $total = \{{MODULE_NAMESPACE}}\{{MODULE}}::get{{MODULE_SIMPLIFY}}s($formData, $sortby, $sorttype, 0, true);

        //get latest account
        ${{MODULE_LOWER}}s = \{{MODULE_NAMESPACE}}\{{MODULE}}::get{{MODULE_SIMPLIFY}}s(
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
        $jsondata['primaryproperty'] = '{{PRIMARY_PROPERTY}}';
        $jsondata['editurlprefix'] = $this->registry->conf['rooturl']
            . $this->registry->module . '/' . $this->registry->controller . '/edit/{{PRIMARY_PROPERTY}}/';

        $jsondata['deleteurlprefix'] = $this->registry->conf['rooturl']
            . $this->registry->module . '/' . $this->registry->controller . '/delete/{{PRIMARY_PROPERTY}}/';

        $jsondata['items'] = array();

        foreach (${{MODULE_LOWER}}s as $my{{MODULE}}) {
            $jsondata['items'][] = array({{JSON_DATA_ASSIGN}}
            );
        }

        header('Content-type: text/json');
        echo json_encode($jsondata);

    }


    public function addAction()
    {
        $error      = array();
        $success    = array();
        $contents   = '';
        $formData   = array();

        if (!empty($_POST['fsubmit'])) {
            if ($_SESSION['{{MODULE_LOWER}}AddToken'] == $_POST['ftoken']) {

                $formData = array_merge($formData, $_POST);

                if ($this->addActionValidator($formData, $error)) {
                    $my{{MODULE_SIMPLIFY}} = new \{{MODULE_NAMESPACE}}\{{MODULE}}();

                    {{ADD_ASSIGN_PROPERTY}}

                    if ($my{{MODULE_SIMPLIFY}}->addData()) {
                        $success[] = $this->registry->lang['controller']['succAdd'];
                        $formData = array();
                    } else {
                        $error[] = $this->registry->lang['controller']['errAdd'];
                    }
                }
            }
        }

        $_SESSION['{{MODULE_LOWER}}AddToken'] = Helper::getSecurityToken();

        $this->registry->smarty->assign(array(
            'formData'      => $formData,{{CONSTANT_CONTROLLER_ASSIGN}}
            'redirectUrl'   => $this->getRedirectUrl(),
            'error'         => $error,
            'success'       => $success
        ));

        $contents .= $this->registry->smarty->fetch($this->registry->smartyController.'add.tpl');
        $this->registry->smarty->assign(array(
            'pageTitle' => $this->registry->lang['controller']['pageTitle_add'],
            'contents'          => $contents
        ));
        $this->registry->smarty->display($this->registry->smartyModule . 'index.tpl');
    }



    public function editAction()
    {
        ${{PRIMARY_PROPERTY}} = (int)$this->registry->router->getArg('{{PRIMARY_PROPERTY}}');
        $my{{MODULE_SIMPLIFY}} = new \{{MODULE_NAMESPACE}}\{{MODULE}}(${{PRIMARY_PROPERTY}});

        $redirectUrl = $this->getRedirectUrl();
        if ($my{{MODULE_SIMPLIFY}}->{{PRIMARY_PROPERTY}} == 0) {
            $redirectMsg = $this->registry->lang['controller']['errNotFound'];
            $this->registry->smarty->assign(array(
                'redirect' => $redirectUrl,
                'redirectMsg' => $redirectMsg,
            ));

            $this->registry->smarty->display('redirect.tpl');
            exit();
        }

        //Record Found
        $error      = array();
        $success    = array();
        $contents   = '';
        $formData   = array();

        $formData['fbulkid'] = array();

        {{EDIT_FORMDATA_INIT}}

        if (!empty($_POST['fsubmit'])) {
            if ($_SESSION['{{MODULE_LOWER}}EditToken'] == $_POST['ftoken']) {
                $formData = array_merge($formData, $_POST);

                if ($this->editActionValidator($formData, $error)) {
                    {{EDIT_ASSIGN_PROPERTY}}

                    if ($my{{MODULE_SIMPLIFY}}->updateData()) {
                        $success[] = $this->registry->lang['controller']['succUpdate'];
                    } else {
                        $error[] = $this->registry->lang['controller']['errUpdate'];
                    }
                }
            }
        }


        $_SESSION['{{MODULE_LOWER}}EditToken'] = Helper::getSecurityToken();//Tao token moi

        $this->registry->smarty->assign(array(
            'formData'  => $formData,
            {{CONSTANT_CONTROLLER_ASSIGN}}
            'redirectUrl'=> $redirectUrl,
            'error'     => $error,
            'success'   => $success,
        ));

        $contents .= $this->registry->smarty->fetch($this->registry->smartyController.'edit.tpl');
        $this->registry->smarty->assign(array(
            'pageTitle' => $this->registry->lang['controller']['pageTitle_edit'],
            'contents'          => $contents
        ));

        $this->registry->smarty->display($this->registry->smartyModule . 'index.tpl');

    }

    public function deleteAction()
    {
        $success = 0;
        $message = '';

        ${{PRIMARY_PROPERTY}} = (int)$this->registry->router->getArg('{{PRIMARY_PROPERTY}}');
        $my{{MODULE_SIMPLIFY}} = new \{{MODULE_NAMESPACE}}\{{MODULE}}(${{PRIMARY_PROPERTY}});
        if ($my{{MODULE_SIMPLIFY}}->{{PRIMARY_PROPERTY}} > 0 && Helper::checkSecurityToken()) {
            //tien hanh xoa
            if ($my{{MODULE_SIMPLIFY}}->delete()) {
                $success = 1;
                $message = str_replace('###{{PRIMARY_PROPERTY}}###', $my{{MODULE_SIMPLIFY}}->{{PRIMARY_PROPERTY}}, $this->registry->lang['controller']['succDelete']);
            } else {
                $message = str_replace('###{{PRIMARY_PROPERTY}}###', $my{{MODULE_SIMPLIFY}}->{{PRIMARY_PROPERTY}}, $this->registry->lang['controller']['errDelete']);
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
                    $my{{MODULE_SIMPLIFY}} = new \{{MODULE_NAMESPACE}}\{{MODULE}}($id);

                    if ($my{{MODULE_SIMPLIFY}}->{{PRIMARY_PROPERTY}} > 0) {
                        //tien hanh xoa
                        if ($my{{MODULE_SIMPLIFY}}->delete()) {
                            $deletedItems[] = $my{{MODULE_SIMPLIFY}}->{{PRIMARY_PROPERTY}};
                        } else {
                            $cannotDeletedItems[] = $my{{MODULE_SIMPLIFY}}->{{PRIMARY_PROPERTY}};
                        }
                    } else {
                        $cannotDeletedItems[] = $my{{MODULE_SIMPLIFY}}->{{PRIMARY_PROPERTY}};
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

    ####################################################################################################
    ####################################################################################################
    ####################################################################################################

    //Kiem tra du lieu nhap trong form them moi
    private function addActionValidator($formData, &$error)
    {
        $pass = true;
        {{ADD_VALIDATOR}}

        return $pass;
    }

    //Kiem tra du lieu nhap trong form cap nhat
    private function editActionValidator($formData, &$error)
    {
        $pass = true;
        {{EDIT_VALIDATOR}}

        return $pass;
    }
}
