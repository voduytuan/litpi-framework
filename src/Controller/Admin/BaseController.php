<?php

/**
 * Base Controller for Admin Module.
 *
 * This file define BaseController Class for Admin Module.
 * All controllers of Admin Module must be extends from this class.
 *
 * @package Litpi\Controller
 * @author Vo Duy Tuan
 * @version 2.0
 *
 */

namespace Controller\Admin;

/**
 * Abstract class of Base Controller of Module Admin
 *
 *
 */
abstract class BaseController extends \Controller\BaseController
{
    /**
     * Khoi tao BaseController
     *
     * @param \Vendor\Litpi\Registry $registry
     */
    public function __construct($registry)
    {
        if (is_null($registry->myUser)) {
            $registry->myUser = $registry->me;
            $registry->smarty->assign('myUser', $registry->me);
        }

        parent::__construct($registry);
    }
}
