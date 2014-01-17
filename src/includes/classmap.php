<?php

function classmap($classname)
{
    //Lowercase all part in classname to prevent some weird case name
    $classname = strtolower($classname);

    //Create by Generator
    $classmapList = array(
        'controller\admin\basecontroller' => 'Controller' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'BaseController.php',
        'controller\admin\codegenerator' => 'Controller' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'CodeGenerator.php',
        'controller\admin\index' => 'Controller' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'Index.php',
        'controller\admin\login' => 'Controller' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'Login.php',
        'controller\admin\null' => 'Controller' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'Null.php',
        'controller\admin\page' => 'Controller' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'Page.php',
        'controller\admin\profile' => 'Controller' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'Profile.php',
        'controller\admin\user' => 'Controller' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'User.php',
        'controller\admin\utility' => 'Controller' . DIRECTORY_SEPARATOR . 'Admin' . DIRECTORY_SEPARATOR . 'Utility.php',
        'controller\basecontroller' => 'Controller' . DIRECTORY_SEPARATOR . 'BaseController.php',
        'controller\site\basecontroller' => 'Controller' . DIRECTORY_SEPARATOR . 'Site' . DIRECTORY_SEPARATOR . 'BaseController.php',
        'controller\site\captcha' => 'Controller' . DIRECTORY_SEPARATOR . 'Site' . DIRECTORY_SEPARATOR . 'Captcha.php',
        'controller\site\index' => 'Controller' . DIRECTORY_SEPARATOR . 'Site' . DIRECTORY_SEPARATOR . 'Index.php',
        'controller\site\logout' => 'Controller' . DIRECTORY_SEPARATOR . 'Site' . DIRECTORY_SEPARATOR . 'Logout.php',
        'controller\site\notfound' => 'Controller' . DIRECTORY_SEPARATOR . 'Site' . DIRECTORY_SEPARATOR . 'NotFound.php',
        'controller\site\install' => 'Controller' . DIRECTORY_SEPARATOR . 'Site' . DIRECTORY_SEPARATOR . 'Install.php'
    );

    if (isset($classmapList[$classname])) {
        return $classmapList[$classname];
    } else {
        return '';
    }
}
