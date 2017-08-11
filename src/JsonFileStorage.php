<?php
namespace WaffleSystems\IO;

use \RuntimeException;

/**
 * Manages the data inside of a json file.
 *
 * Implements StorageInterface to provide extra functionality for storing data.
 */
class JsonFileStorage implements StorageInterface
{

    private $filename;
    private $createIfNotExists;

    private $contents;

    /**
     * Instantiates a new JsonFileStorage to store data inside of a json file.
     *
     * @param string $filename The filename to which the json data will be saved.
     */
    public function __construct($filename)
    {
        $this->filename          = (string) $filename;
        $this->createIfNotExists = false;
    }

    /**
     * Returns the file name this class will use to read and write data.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Tells whether this file storage should create the file if it doesn't exist.
     *
     * @param boolean $value True, if non-existent files are to be created.
     * @return void
     */
    public function setCreateIfNotExists($value)
    {
        $this->createIfNotExists = $value ? true : false;
    }

    /**
     * Returns whether a file will be created if it doesn't exist.
     *
     * @return boolean
     */
    public function shouldCreateIfNotExists()
    {
        return $this->createIfNotExists;
    }

    /**
     * Opens the json file.
     *
     * If the file isn't found, and the {@link createIfNotExists} method is
     * called with true, then the file will be created when data is written to it.
     *
     * If the file is empty, then this class will assume the contents to be a
     * json object.
     *
     * @return void
     */
    public function open()
    {
        if (is_file($this->filename)) {
            $fileData = file_get_contents($this->filename);
            if (false === $fileData) {
                throw new IOException('Unable to get the contents of the json file!');
            }
        } else {
            $fileData = null;
            if (!$this->createIfNotExists) {
                throw new FileNotFoundException("The file doesn't exist!");
            }
        }

        if (0 === strlen($fileData)) {
            $this->contents = [];
        } else {
            $this->contents = json_decode($fileData, true);

            if (null === $this->contents) {
                throw new RuntimeException('The format of the JSON file is invalid!');
            }
        }
    }

    /**
     * Overwrites a record with the key parameter.
     *
     * If the record doesn't exist, it will be created automatically.
     * The entire record will be replaced by the value parameter.
     *
     * @param string $key The record's key.
     * @param mixed $value The value to be replaced with the record's contents.
     * @return void
     */
    public function set($key, $value)
    {
        $this->assertOpened();
        $this->contents[(string) $key] = $value;
    }

    /**
     * Returns a record that originates from the opened json file.
     *
     * If the key doesn't exist, then null will be returned.
     *
     * @param string $key The key of the record.
     * @return mixed
     */
    public function get($key)
    {
        $this->assertOpened();
        $key = (string) $key;
        if (isset($this->contents[$key])) {
            return $this->contents[$key];
        }
        return null;
    }

    /**
     * Returns whether the record exists with the given key.
     *
     * @param string $key The key of the record.
     * @return boolean
     */
    public function has($key)
    {
        $this->assertOpened();
        return isset($this->contents[(string) $key]);
    }

    /**
     * Deletes the record with the given key.
     *
     * @param string $key The key of the record.
     * @return void
     */
    public function delete($key)
    {
        $this->assertOpened();
        unset($this->contents[(string) $key]);
    }

    /**
     * Deletes all of the records.
     *
     * @return void
     */
    public function clear()
    {
        $this->assertOpened();
        foreach ($this->contents as $key => $value) {
            unset($this->contents[$key]);
        }
    }

    /**
     * Writes any pending changes to the opened json file
     * and disposes of the class.
     *
     * If this object wasn't opened, then this method will do nothing.
     * @return void
     */
    public function close()
    {
        if (null !== $this->contents) {
            $result = file_put_contents($this->filename, json_encode($this->contents));
            if (false === $result) {
                throw new IOException('Unable to write to file!');
            }
            $this->contents = null;
        }
    }

    private function assertOpened()
    {
        if (null === $this->contents) {
            throw new RuntimeException('This object has not been opened!');
        }
    }
}
