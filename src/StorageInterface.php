<?php
namespace WaffleSystems\IO;

/**
 * Implementors can implement methods to read and write data in a medium with keys and values.
 */
interface StorageInterface
{

    /**
     * @return void
     */
    public function open();

    /**
     * @return void
     */
    public function set($key, $value);

    /**
     * @return mixed
     */
    public function get($key);

    /**
     * @return boolean
     */
    public function has($key);

    /**
     * @return void
     */
    public function delete($key);

    /**
     * @return void
     */
    public function close();
}
