<?php

function rewriteruleParsing($route)
{
    global $setting, $conf;

    $parts = explode('/', $route);

    //If first part of url is main module, we just return,
    // do not process when route start with module in url, You can change logic if you want
    if (in_array($parts[0], array('site', 'admin'))) {
        return $route;
    }

    //Your custom parsing from $parts here to build new $route and extract important data
    //...

    return $route;
}
