<?php

namespace Vendor\Litpi;

class ResourceServer
{
    public static function getUrl($resourceid, $type = '')
    {
        global $registry;

        $rooturl = '';
        if ($resourceid == 0) {
            $rooturl = $registry->conf['rooturl'];
        } else {
            $rooturl = $registry->conf['rooturl'];
        }

        return $rooturl;
    }
}
