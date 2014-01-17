<?php

spl_autoload_register('autoloadlitpi');

function autoloadlitpi($classname)
{
    $filepathFromMapping = classmap($classname);

    if ($filepathFromMapping == '') {
        //Process Namespace Directoryseparator
        $namepart = explode('\\', $classname);

        $filepath = SITE_PATH;
        for ($i = 0; $i < count($namepart); $i++) {
            $filepath .= trim($namepart[$i]);

            if ($i == count($namepart) - 1) {
                $filepath .= '.php';
            } else {
                $filepath .= DIRECTORY_SEPARATOR;
            }
        }
    } else {
        $filepath = SITE_PATH . $filepathFromMapping;
    }

    if (is_readable($filepath)) {
        include_once($filepath);

        return true;
    } else {
        return false;
    }
}
