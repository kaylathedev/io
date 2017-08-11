<?php
namespace WaffleSystems\IO;

use \RuntimeException;

class MemoryStorage implements StorageInterface
{

    private static $closedAccessError =
            'The data can not be modified or accessed after being closed.';

    private $opened;
    private $contents = [];

    public function getContents()
    {
        return $this->contents;
    }

    public function open()
    {
        $this->opened = true;
    }

    public function set($key, $value)
    {
        if (!$this->opened) {
            throw new RuntimeException('The memory storage can not be modified after being closed.');
        }
        $this->contents[$key] = $value;
    }

    /**
     * @return mixed|null
     */
    public function get($key)
    {
        if (!$this->opened) {
            throw new RuntimeException(self::$closedAccessError);
        }
        return isset($this->contents[$key]) ? $this->contents[$key] : null;
    }

    public function has($key)
    {
        if (!$this->opened) {
            throw new RuntimeException(self::$closedAccessError);
        }
        return isset($this->contents[$key]);
    }

    public function delete($key)
    {
        if (!$this->opened) {
            throw new RuntimeException(self::$closedAccessError);
        }
        unset($this->contents[$key]);
    }

    public function close()
    {
        $this->opened = false;
    }
}
