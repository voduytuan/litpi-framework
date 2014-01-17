<?php

namespace Vendor\Litpi;

require 'includes/config.php';
require 'includes/setting.php';
require 'includes/db.php';
require 'includes/permission.php';

require 'includes/classmap.php';
require 'includes/autoload.php';
require 'includes/rewriterule.php';
require 'includes/startup.php';
include 'libs/pqp/classes/PhpQuickProfiler.php';

$myDosDetector = new DosDetector();
$myDosDetector->run($conf['rooturl'] . 'accessdeny.html');

if (isset($_GET['xprofiler'])) {
    $pqpProfiler = new \PhpQuickProfiler(\PhpQuickProfiler::getMicroTime(), SITE_PATH . 'libs/pqp/');
}

# Load router
$router = new Router($registry);
$registry->router = $router;
$router->delegate();

if (isset($_GET['xprofiler'])) {
    $pqpProfiler->display();
}
