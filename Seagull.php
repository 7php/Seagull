<?php

/**
 *
 */
class Seagull
{
    protected $separator;

    /** the complete configuration array */
    protected $config = array();

    public function __construct($data = array(), $separator = '.')
    {
        $this->separator = $separator;
        if(is_array($data)) {
            $this->config = $data;
        }
    }

    /**
     * Recursive function to get an element from, or set an element in the config by its path
     *
     * @access protected
     *
     * @param string $path
     *    path to the element in the array (path.to.element)
     * @param array|string &$conf
     *    the configuration or part of the configuration (on recursion)
     * @param mixed $newValue (optional)
     *    the new value to set in the config on the specified path
     *
     * @return mixed
     *    the value found in the config at the specified path, or null if the path doesn't exist
     */
    protected function conf($path, &$conf, $newValue = null) {

        if(!$path) return $this->config;


        $sep = $this->separator;


        $path = explode($sep, $path);

        $first = array_shift($path);

        if(isset($conf[$first])) {

            // there's more left on the path, keep following it
            if(count($path)) {
                goto recurse;
            }
            
            if($newValue === '[[seagull-delete]]') {
                // for deleting values
                unset($conf[$first]);
                return;
            } elseif($newValue !== null) {
                $conf[$first] = $newValue;
            }

            return $conf[$first];
        } elseif($newValue !== null) {
            // a new piece of path, create it
            if(count($path)) {
                $conf[$first] = array();
                goto recurse;
            }
            return $conf[$first] = $newValue;
        }

        return null;

        recurse:
        return $this->conf(implode($sep, $path), $conf[$first], $newValue); // <--- RECURSE!!!
    }

    /**
     * Get a value from the config, from a specific path.
     *
     * @param string $path
     *    path to the element in the config (path.to.element)
     *
     * @return mixed
     *    the value found in the config at the specified path, or null if the path doesn't exist
     */
    public function get($path = null)
    {
        return $this->conf($path, $this->config, null);
    }

    /**
     * Set a value in the config, on a specific path.
     *
     * @param string $path
     *    path to the element in the config (path.to.element)
     * @param mixed $newValue (optional)
     *    the new value to set in the config on the specified path
     *
     * @return mixed
     *    the value found in the config at the specified path, or null if the path doesn't exist
     */
    public function set($path, $value)
    {
        $this->conf($path, $this->config, $value);
        return $this;
    }

    public function delete($path)
    {
        $this->conf($path, $this->config, '[[seagull-delete]]');
        return $this;
    }

    public function setSeparator($sep = '.')
    {
        if(is_string($sep) && strlen($sep) == 1) {
            $this->separator = $sep;
        }
        return $this;
    }
}