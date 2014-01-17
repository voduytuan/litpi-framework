<?php

namespace Vendor\Litpi\Model;

abstract class BaseModel
{
    protected $db;

    public function __construct()
    {
        global $db;

        $this->db = $db;
    }

    public function __sleep()
    {
           $this->db = null;

           return $this;
    }

    public function copy(BaseModel $object)
    {
        foreach (get_object_vars($object) as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function getDb()
    {
        return $GLOBALS['db'];
    }

    /**
    * Luu thong tin vao cache
    *
    */
    public static function cacheSet($key, $value)
    {
        global $registry;

        $myCacher = new \Vendor\Litpi\Cacher($key);

        return $myCacher->set($value, $registry->setting['site']['apcUserCacheTimetolive']);
    }
}
