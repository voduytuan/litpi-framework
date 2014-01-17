<?php

define('HOST', 'localhost/litpiproject/litpi2/src');
define('TABLE_PREFIX', 'lit_');
define('SITE_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
define('SUBDOMAIN', array_shift(explode(".", $_SERVER['HTTP_HOST'])));
define('PROTOCOL', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http');

$conf = array();
$conf['host'] = (SUBDOMAIN == 'm') ? 'm.' . HOST : HOST;
$conf['rooturl'] = PROTOCOL . '://' . $conf['host'] . '/';
$conf['rooturl_admin'] = PROTOCOL . '://' . $conf['host'] . '/admin/';
$conf['defaultLang'] = 'vn';


error_reporting(E_ERROR);
ini_set("display_errors", 1);
ini_set('session.name', 'SHASH');
ini_set('session.use_only_cookies', true);
ini_set('session.use_trans_sid', false);
date_default_timezone_set('Asia/Ho_Chi_Minh');
set_time_limit(30);


