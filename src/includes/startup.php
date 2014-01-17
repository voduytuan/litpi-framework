<?php

namespace Vendor\Litpi;

$db = new MyPdoProxy();
$db->addMaster($conf['db']['host'], $conf['db']['user'], $conf['db']['pass'], $conf['db']['name']);
$db->addSlave($conf['db']['host'], $conf['db']['user'], $conf['db']['pass'], $conf['db']['name']);


//INIT REGISTRY VARIABLE - MAIN STORAGE OF APPLICATION
$registry = new Registry();
$registry->https = (PROTOCOL == 'https' ? true : false);
$registry->conf = $conf;
$registry->db = $db;
$registry->setting = $setting;

//Init session
session_start();

// Url rewrite
// Declared in /includes/rewriterule.php
$route = rewriteruleParsing(Router::initRoute('site'));

// Parsing Route to get MODULE, CONTROLLER & ACTION
$parts = explode('/', $route);
if ($parts[0]) {
    $GLOBALS['module'] = $parts[0];
}

if (!empty($parts[1])) {
    $GLOBALS['controller'] = $parts[1];
    if (!empty($parts[2])) {
        $GLOBALS['action'] = $parts[2];
    } else {
        $GLOBALS['action'] = 'index';
        $route = $GLOBALS['module'] . '/' . $GLOBALS['controller'] . '/' . 'index';
    }
} else {
    $GLOBALS['controller'] = 'index';
    $GLOBALS['action'] = 'index';
    $route = $GLOBALS['module'] . '/' . 'index' . '/' . 'index';
}

/////////////////////////////////////////////
//  LANGUAGE DETECTING
if (isset($_GET['language'])) {
    $_SESSION['language'] = $_GET['language'];
    setcookie('language', $_GET['language'], time() + 24 * 3600, '/');
} elseif (isset($_POST['language'])) {
    $_SESSION['language'] = $_POST['language'];
    setcookie('language', $_POST['language'], time() + 24 * 3600, '/');
}

if (isset($_SESSION['language'])) {
    $langCode = $_SESSION['language'];
} elseif (isset($_COOKIE['language'])) {
    $langCode = $_COOKIE['language'];
} else {
    $langCode = $conf['defaultLang'];
}

//Ensure langCode always two character, ex: vn, en, fr...
$langCode = substr($langCode, 0, 2);

//declare language variable
$lang = array();
$lang['global'] = Helper::getLangContent('language' . DIRECTORY_SEPARATOR  . $langCode . DIRECTORY_SEPARATOR, 'global');
$defaultDirectoryLang = strtolower($GLOBALS['module']);

$lang['default'] = Helper::GetLangContent('language' . DIRECTORY_SEPARATOR . $langCode . DIRECTORY_SEPARATOR . $defaultDirectoryLang . DIRECTORY_SEPARATOR, 'default');
$lang['controller'] = Helper::GetLangContent('language' . DIRECTORY_SEPARATOR . $langCode . DIRECTORY_SEPARATOR . strtolower($GLOBALS['module']) . DIRECTORY_SEPARATOR, strtolower($GLOBALS['controller']));


///////////////////////////////////////////
//  MOBILE DETECTING
$registry->mobiledetect = $mobiledetect = new \Vendor\Other\MobileDetect();
if ($setting['site']['enableMobileWebRedirect'] && $mobiledetect->isMobile() && SUBDOMAIN != 'm') {
    //check force desktopsite is disable
    if (!isset($_COOKIE['forcedesktop']) || $_COOKIE['forcedesktop'] == 0) {
        //begin redirect link to mobile version
        $curPageURL = Helper::curPageURL();
        $curPageURL = str_replace(array('http://', 'https://'), array('http://m.', 'https://m.'), $curPageURL);
        header('location: ' . $curPageURL);
    }
}


$me = new \Model\User();
$me->updateFromSession();
$me->checkPerm();

$registry->me = $me;
$registry->lang = $lang;
$registry->langCode = $langCode;
$registry->module = strtolower($GLOBALS['module']);
$registry->controller = strtolower($GLOBALS['controller']);
$registry->action = strtolower($GLOBALS['action']);
$registry->cart = new CookieCart();

//////////////////
//Include Smarty class
include(SITE_PATH. 'libs' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'Smarty.class.php');
$smarty = new \Smarty();

//set current template
$currentTemplate = 'default';
$registry->currentTemplate =  $registry->getResourceHost('static');

$smarty->template_dir = 'templates' . DIRECTORY_SEPARATOR . $currentTemplate;
$smarty->compile_dir = 'templates'.DIRECTORY_SEPARATOR.'_core'.DIRECTORY_SEPARATOR.'templates_c' . DIRECTORY_SEPARATOR;
$smarty->config_dir = 'templates'.DIRECTORY_SEPARATOR.'_core'.DIRECTORY_SEPARATOR.'configs' . DIRECTORY_SEPARATOR;
$smarty->cache_dir = 'templates'.DIRECTORY_SEPARATOR.'_core'.DIRECTORY_SEPARATOR.'cache' . DIRECTORY_SEPARATOR;
$smarty->compile_id = $currentTemplate;	//seperate compiled template file
$smarty->error_reporting = E_ALL ^ E_NOTICE;
$smarty->compile_check = $setting['site']['smartyCompileCheck'];
$smarty->assign(array(
    'me'            => $me,
    'conf'          => $conf,
    'registry'      => $registry,
    'langCode'      => $langCode,
    'lang'          => $lang,
    'setting'	    => $setting,
    'controller'    => strtolower($GLOBALS['controller']),
    'module'        => strtolower($GLOBALS['module']),
    'action'        => strtolower($GLOBALS['action']),
    'redirect'      => base64_encode($GLOBALS['route']),
    'currentTemplate'=> $registry->getResourceHost('static'),
    'templateName'  => $currentTemplate,
    'currentUrl'    => \Vendor\Litpi\Helper::curPageURL(),
    'imageDir'      => $registry->getResourceHost('static') . (SUBDOMAIN == 'm' ? 'm/' : '') . 'images/',
    'paginateLang' 	=> array(
        'first' 			=> $lang['global']['navpageFirst'],
        'last' 				=> $lang['global']['navpageLast'],
        'firstTooltip' 		=> $lang['global']['navpageFirstTooltip'],
        'lastTooltip' 		=> $lang['global']['navpageLastTooltip'],
        'previous'			=> $lang['global']['navpagePrevious'],
        'next' 				=> $lang['global']['navpageNext'],
        'previousTooltip' 	=> $lang['global']['navpagePreviousTooltip'],
        'nextTooltip' 		=> $lang['global']['navpageNextTooltip'],
        'pageTooltip' 		=> $lang['global']['navpagePageTooltip']
    )
));

$registry->smarty = $smarty;
