<?php

function classmap($classname)
{
    //Lowercase all part in classname to prevent some weird case name
    $classname = strtolower($classname);

    //Create by Generator
    $classmapList = array(
        {{CLASSMAP_ARRAY_ELEMENTS}}
    );

    if (isset($classmapList[$classname])) {
        return $classmapList[$classname];
    } else {
        return '';
    }
}
