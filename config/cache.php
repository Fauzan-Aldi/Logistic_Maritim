<?php
// Cache configuration

require_once 'environment.php';

class Cache {
    private static $instance = null;
    private $redis = null;
    private $enabled = false;
    
    private function __construct() {
        if (CACHE_ENABLED) {
            try {
                $this->redis = new Redis();
                $this->redis->connect('127.0.0.1', 6379);
                $this->enabled = true;
            } catch (Exception $e) {
                error_log("Cache connection failed: " . $e->getMessage());
                $this->enabled = false;
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function get($key) {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            $value = $this->redis->get($key);
            return $value !== false ? json_decode($value, true) : false;
        } catch (Exception $e) {
            error_log("Cache get failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function set($key, $value, $ttl = null) {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            $value = json_encode($value);
            if ($ttl === null) {
                $ttl = CACHE_LIFETIME;
            }
            return $this->redis->setex($key, $ttl, $value);
        } catch (Exception $e) {
            error_log("Cache set failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($key) {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->del($key);
        } catch (Exception $e) {
            error_log("Cache delete failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function exists($key) {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->exists($key);
        } catch (Exception $e) {
            error_log("Cache exists failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function clear() {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->flushDB();
        } catch (Exception $e) {
            error_log("Cache clear failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function increment($key) {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->incr($key);
        } catch (Exception $e) {
            error_log("Cache increment failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function decrement($key) {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->decr($key);
        } catch (Exception $e) {
            error_log("Cache decrement failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function getMultiple($keys) {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            $values = $this->redis->mget($keys);
            return array_map(function($value) {
                return $value !== false ? json_decode($value, true) : false;
            }, array_combine($keys, $values));
        } catch (Exception $e) {
            error_log("Cache getMultiple failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function setMultiple($items, $ttl = null) {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            if ($ttl === null) {
                $ttl = CACHE_LIFETIME;
            }
            
            $pipeline = $this->redis->multi(Redis::PIPELINE);
            foreach ($items as $key => $value) {
                $pipeline->setex($key, $ttl, json_encode($value));
            }
            return $pipeline->exec();
        } catch (Exception $e) {
            error_log("Cache setMultiple failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteMultiple($keys) {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->del($keys);
        } catch (Exception $e) {
            error_log("Cache deleteMultiple failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function getStats() {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->info();
        } catch (Exception $e) {
            error_log("Cache getStats failed: " . $e->getMessage());
            return false;
        }
    }
}

// Cache helper functions
function cache_get($key) {
    return Cache::getInstance()->get($key);
}

function cache_set($key, $value, $ttl = null) {
    return Cache::getInstance()->set($key, $value, $ttl);
}

function cache_delete($key) {
    return Cache::getInstance()->delete($key);
}

function cache_exists($key) {
    return Cache::getInstance()->exists($key);
}

function cache_clear() {
    return Cache::getInstance()->clear();
}

function cache_increment($key) {
    return Cache::getInstance()->increment($key);
}

function cache_decrement($key) {
    return Cache::getInstance()->decrement($key);
}

function cache_get_multiple($keys) {
    return Cache::getInstance()->getMultiple($keys);
}

function cache_set_multiple($items, $ttl = null) {
    return Cache::getInstance()->setMultiple($items, $ttl);
}

function cache_delete_multiple($keys) {
    return Cache::getInstance()->deleteMultiple($keys);
}

function cache_get_stats() {
    return Cache::getInstance()->getStats();
} 