<?php
namespace Rpnzl;

use RuntimeException;

/**
 * Provides access to default and environment
 * configuration.
 */

class Config
{
    /**
     * 
     */

    private $env;

    /**
     * 
     */

    private $path;

    /**
     * 
     */

    private $cache = array();

    /**
     * 
     */

    public function __construct($path, $env = 'development')
    {
        $this->setPath($path);
        $this->setEnv($env);
    }

    /**
     * 
     */

    public function setPath($path)
    {
        if (!is_dir($path)) {
            throw new RuntimeException('Path provided is not a valid directory.');
        }

        $this->path = (substr($path, -1, 1) === DS) ? $path : $path.DS;
    }

    /**
     * 
     */

    public function setEnv($env)
    {
        $this->cache = array();
        $this->env = strtolower($env);
    }

    /**
     * 
     */

    public function get($key = null)
    {
        // Get entire cache
        if (!$key) return $this->cache;

        // Loop key and find value
        $current = $this->cache;
        foreach (array_filter(explode('.', $key)) as $k) {
            if (array_key_exists($k, $current)) {
                $current = $current[$k];
            } else {
                $path = $this->path.$k.'.php';
                $base = file_exists($path) ? include $path : array();
                $path = $this->path.$this->env.DS.$k.'.php';
                $env  = file_exists($path) ? include $path : array();

                $this->cache[$k] = array_replace_recursive($base, $env);
                $current = $this->cache[$k];
            }
        }

        return $current;
    }
}

/* End of file Config.php */
