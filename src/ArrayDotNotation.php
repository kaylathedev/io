<?php
namespace WaffleSystems\IO;

use \RuntimeException;

class ArrayDotNotation
{

    private $data;

    public function __construct(array $data = null)
    {
        if (null === $data) {
            $data = [];
        }
        $this->data = $data;
    }

    /**
     * @param string $path
     */
    public function get($path, $default = null)
    {
        if (0 === strlen($path)) {
            return $this->data;
        }
        $pathNodes = explode('.', $path);
        $endNode   = array_pop($pathNodes);

        $current = $this->getLastNode($pathNodes, function() {
            /* Lets the method know to return immediately, if a node is missing. */
            return true;
        });
        /* This function is called when the second to last node is reached. */
        if (isset($current[$endNode])) {
            return $current[$endNode];
        }
        return $default;
    }

    public function has($path)
    {
        if (0 === strlen($path)) {
            return $this->data;
        }
        $pathNodes = explode('.', $path);
        $endNode   = array_pop($pathNodes);

        $current = $this->getLastNode($pathNodes, function() {
            /* Lets the method know to return immediately, if a node is missing. */
            return true;
        });
        /* This function is called when the second to last node is reached. */
        return isset($current[$endNode]);
    }

    /**
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public function set($path, $value)
    {
        if (0 === strlen($path)) {
            $this->data = $value;
            return;
        }
        $pathNodes = explode('.', $path);
        $endNode   = array_pop($pathNodes);
        $current   =& $this->getLastNode($pathNodes, function() {
            /* Sets the current node to an array, if a node is missing. */
            return [];
        });
        if (is_array($current)) {
            $current[$endNode] = $value;
        } else {
            throw new RuntimeException('The path can not be resolved!');
        }
    }

    public function delete($path)
    {
        if (0 === strlen($path)) {
            $this->data = [];
            return;
        }
        $pathNodes = explode('.', $path);
        $endNode   = array_pop($pathNodes);

        $current =& $this->getLastNode($pathNodes, function() {
            /* Lets the method know to return immediately, if a node is missing. */
            return true;
        });
        if (null !== $current) {
            unset($current[$endNode]);
        }
    }

    public function clear()
    {
        $this->data = [];
    }

    public function dump()
    {
        return $this->data;
    }

    /**
     * @param array $nodes
     * @param callable $nodeMissingCallback
     */
    private function &getLastNode(array $nodes, $nodeMissingCallback)
    {
        $current =& $this->data;

        foreach ($nodes as $nodeName) {
            if (!is_array($current)) {
                throw new RuntimeException('The path can not be resolved!');
            }
            if (!isset($current[$nodeName])) {
                $solution = call_user_func($nodeMissingCallback);
                if (true === $solution) {
                    /**
                     * TODO: Find some way (that's not insane) to return false|null
                     * while being able to return references.
                     * I'm ripping my hair out!!!
                     */
                    $null = null;
                    return $null;
                }
                if (is_array($solution)) {
                    $current[$nodeName] = $solution;
                }
            }
            $current =& $current[$nodeName];
        }
        return $current;
    }
}
