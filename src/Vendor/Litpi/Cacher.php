<?php

namespace Vendor\Litpi;

class Cacher
{
    const STORAGE_FILE = 1;
    const STORAGE_APC = 3;
    const STORAGE_REDIS = 5;
    const STORAGE_MEMCACHED = 7;

    public $storage;
    public $key = '';
    public static $redis = null;
    public static $memcached = null;

    /**
     * @param $storageEngineConfig array: include 2 elements: array('ip' => SERVER_IP_ADDRESS, 'port' => PORT_NUMBER)
     */
    public function __construct($key, $storageEngine = 0, $storageEngineConfig = null)
    {
        //Set default Storage Engine for cache
        if ($storageEngine == 0) {
            $storageEngine = self::STORAGE_APC;
        }

        if ($storageEngine != self::STORAGE_FILE
            && $storageEngine != self::STORAGE_APC
            && $storageEngine != self::STORAGE_REDIS
            && $storageEngine != self::STORAGE_MEMCACHED) {
            $this->storageException();
        } else {
            if ($storageEngine == self::STORAGE_REDIS) {
                if (self::$redis == null) {
                    self::$redis = self::getRedisInstance($storageEngineConfig);
                }
            } elseif ($storageEngine == self::STORAGE_MEMCACHED) {
                if (self::$memcached == null) {
                    self::$memcached = self::getMemcachedInstance($storageEngineConfig);
                }
            }

            $this->storage = $storageEngine;
            $this->key = $key;
        }
    }

    public static function getRedisInstance($storageEngineConfig = null)
    {
        global $conf;

        $serverIp = $storageEngineConfig['ip'];
        $serverPort = $storageEngineConfig['port'];

        $output = null;

        try {
            $redis = new \Redis();
            $redis->connect($serverIp, $serverPort);
            $output = $redis;
        } catch (Exception $e) {
            //Can not connect to Redis Server
            echo('Can not connect to Redis Server');
        }

        return $output;
    }

    public static function getMemcachedInstance($storageEngineConfig = null)
    {
        global $conf;

        $serverIp = $storageEngineConfig['ip'];
        $serverPort = $storageEngineConfig['port'];

        $output = null;
        try {
            $memcached = new \Memcached();
            $memcached->addServer($serverIp, $serverPort);

            $output = $memcached;
        } catch (Exception $e) {
            //Can not connect to Memcache Server
            echo('Can not connect to Memcache Server');
        }

        return $output;
    }

    /**
     * Get a value saved in cache
     */
    public function get()
    {
        $output = null;
        if ($this->storage == self::STORAGE_FILE) {
            //Not implemented
        } elseif ($this->storage == self::STORAGE_APC) {
            $output = apc_fetch($this->key);
        } elseif ($this->storage == self::STORAGE_REDIS) {
            if (self::$redis != null) {
                $output = self::$redis->get($this->key);
            }
        } elseif ($this->storage == self::STORAGE_MEMCACHED) {
            if (self::$memcached != null) {
                $output = self::$memcached->get($this->key);
            }
        } else {
            $this->storageException();
        }

        return $output;
    }

    /**
     * Set a value to a key
     */
    public function set($value, $duration = 9000)
    {
        $output = null;

        if ($this->storage == self::STORAGE_FILE) {
            //Not implemented
        } elseif ($this->storage == self::STORAGE_APC) {
            $output = apc_store($this->key, $value, $duration);
        } elseif ($this->storage == self::STORAGE_REDIS) {
            if (self::$redis != null) {
                self::$redis->set($this->key, $value, $duration);
            }
        } elseif ($this->storage == self::STORAGE_MEMCACHED) {
            if (self::$memcached != null) {
                self::$memcached->set($this->key, $value, $duration);
            }
        } else {
            $this->storageException();
        }

        return $output;
    }

    /**
     * Check a key is existed or not
     */
    public function check()
    {
        $output = false;

        if ($this->storage == self::STORAGE_FILE) {
            //Not implemented
        } elseif ($this->storage == self::STORAGE_APC) {
            $output = apc_exists($this->key);
        } elseif ($this->storage == self::STORAGE_REDIS && self::$redis != null) {
            $output = self::$redis->exists($key);
        } elseif ($this->storage == self::STORAGE_MEMCACHED && self::$memcached != null) {
            $output = self::$memcached->get($key) !== false;
        } else {
            $this->storageException();
        }

        return $output;
    }

    /**
     * Clear a cache
     */
    public function clear()
    {
        $output = null;

        if ($this->storage == self::STORAGE_FILE) {
            //Not implemented
        } elseif ($this->storage == self::STORAGE_APC) {
            $output = apc_delete($this->key);
        } elseif ($this->storage == self::STORAGE_REDIS && self::$redis != null) {
            self::$redis->delete($key);
        } elseif ($this->storage == self::STORAGE_MEMCACHED && self::$memcached != null) {
            self::$memcached->delete($key);
        } else {
            $this->storageException();
        }

        return $output;
    }

    private function storageException()
    {
        throw new \Exception('Storage Engine is not valid');
    }
}
