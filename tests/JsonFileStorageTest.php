<?php
namespace WaffleSystems\Tests\IO;

use org\bovigo\vfs\vfsStream;
use WaffleSystems\IO\JsonFileStorage;

class JsonFileStorageTest extends \PHPUnit_Framework_TestCase
{

    private $storage;
    private $filename;
    private $root;

    public function __construct()
    {
        $this->root = vfsStream::setup('example_dir');
        $this->filename = vfsStream::url('example_dir/test.txt');
    }

    public function setUp()
    {
        $this->storage = new JsonFileStorage($this->filename);
    }

    private function createTestData()
    {
    }

    protected function tearDown()
    {
        $this->storage->close();
    }

    private function checkIfMethodThrowsException($exception, $method, array $arguments = [])
    {
        $this->setExpectedException($exception);
        call_user_func_array([$this->storage, $method], $arguments);
    }

    /* START GROUP : Testing for RuntimeException, before opening the object. */

    public function testHasBeforeOpening()
    {
        $this->checkIfMethodThrowsException('RuntimeException', 'has', ['foo']);
    }

    public function testSetBeforeOpening()
    {
        $this->checkIfMethodThrowsException('RuntimeException', 'set', ['foo', 'bar']);
    }

    public function testGetBeforeOpening()
    {
        $this->checkIfMethodThrowsException('RuntimeException', 'get', ['foo']);
    }

    public function testDeleteBeforeOpening()
    {
        $this->checkIfMethodThrowsException('RuntimeException', 'delete', ['foo']);
    }

    public function testClearBeforeOpening()
    {
        $this->checkIfMethodThrowsException('RuntimeException', 'clear');
    }

    /* END GROUP */


    /* START GROUP : Creation and Closing */

    public function testOpen()
    {
        $this->setExpectedException('WaffleSystems\\IO\\FileNotFoundException');
        $this->storage->open();
    }

    public function testOpenWhenFileNotExists()
    {
        $this->assertFileNotExists($this->filename);
        $this->storage->setCreateIfNotExists(true);
        $this->storage->open();

        $this->assertFileNotExists($this->filename);

        $this->storage->close();

        $this->assertFileExists($this->filename);
        $this->assertSame(file_get_contents($this->filename), '[]');
    }

    public function testOpenWithEmptyFile()
    {
        touch($this->filename);

        $this->storage->open();
        $this->storage->close();

        $this->assertSame(file_get_contents($this->filename), '[]');
    }

    /* END GROUP */


    /* START GROUP : Properties */

    public function testGetCreateIfNotExists()
    {
        $this->assertFalse($this->storage->shouldCreateIfNotExists());
    }

    public function testSetCreateIfNotExists()
    {
        $this->storage->setCreateIfNotExists(true);
        $this->assertTrue($this->storage->shouldCreateIfNotExists());
    }

    public function testGetFilename()
    {
        $this->assertSame($this->filename, $this->storage->getFilename());
    }

    /* END GROUP */


    /* START GROUP : Empty Files */

    public function testHasWhenEmpty()
    {
        touch($this->filename);
        $this->storage->open();
        $this->assertSame(false, $this->storage->has('foo'));
    }

    public function testGetWhenEmpty()
    {
        touch($this->filename);
        $this->storage->open();
        $this->assertSame(null, $this->storage->get('foo'));
    }

    public function testDeleteWhenEmpty()
    {
        touch($this->filename);
        $this->storage->open();
        $this->storage->delete('foo');
    }

    /* END GROUP */


    /* START GROUP : Get and Set */

    public function testGetSet()
    {
        $this->storage->open();

        $fooValue = 'Test Text';
        $barValue = [9 => 'a', 'b' => ['wat...']];

        $this->storage->set('foo', $fooValue);
        $this->storage->set('bar', $barValue);

        $this->assertSame($fooValue, $this->storage->get('foo'));
        $this->assertSame($barValue, $this->storage->get('bar'));

        $this->storage->close();

        $this->storage->open();

        $this->assertSame($fooValue, $this->storage->get('foo'));
        $this->assertSame($barValue, $this->storage->get('bar'));
    }

    public function testOverwrite()
    {
        $this->storage->open();

        $fooValue = 'Test Text';
        $fooValueB = 'TestB Text2';

        $this->storage->set('foo', $fooValue);
        $this->assertSame($fooValue, $this->storage->get('foo'));

        $this->storage->set('foo', $fooValueB);
        $this->assertSame($fooValueB, $this->storage->get('foo'));

        $this->storage->close();

        $this->storage->open();

        $this->assertSame($fooValueB, $this->storage->get('foo'));
    }

    public function testDelete()
    {
        $this->storage->open();

        $fooValue = 'Test Text';
        $barValue = [9 => 'a', 'b' => ['wat...']];

        $this->storage->set('foo', $fooValue);
        $this->storage->set('bar', $barValue);

        $this->assertSame($fooValue, $this->storage->get('foo'));
        $this->assertSame($barValue, $this->storage->get('bar'));

        $this->storage->delete('foo');

        $this->assertSame(null, $this->storage->get('foo'));
        $this->assertSame($barValue, $this->storage->get('bar'));

        $this->storage->close();

        $this->storage->open();

        $this->assertSame(null, $this->storage->get('foo'));
        $this->assertSame($barValue, $this->storage->get('bar'));
    }

    public function testClear()
    {
        $this->storage->open();

        $fooValue = 'Test Text';
        $barValue = [9 => 'a', 'b' => ['wat...']];

        $this->storage->set('foo', $fooValue);
        $this->storage->set('bar', $barValue);

        $this->assertSame($fooValue, $this->storage->get('foo'));
        $this->assertSame($barValue, $this->storage->get('bar'));

        $this->storage->clear();

        $this->storage->close();

        $this->storage->open();

        $this->assertSame(null, $this->storage->get('foo'));
        $this->assertSame(null, $this->storage->get('bar'));
    }

    /* END GROUP */

}
