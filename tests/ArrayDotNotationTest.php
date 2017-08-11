<?php
namespace WaffleSystems\IO\Tests;

use WaffleSystems\IO\ArrayDotNotation;

class ArrayDotNotationTest extends \PHPUnit_Framework_TestCase
{

    private function getTestLines()
    {
        return [
            'Value for something.',
            78,
            '3',
            true,
            'Am I a drunk programmer?',
            'This is a crazy test.',
            'He went in arrays deep.',
            false
        ];
    }

    public function testConstructorWithArray()
    {
        $testLines = $this->getTestLines();
        $data = new ArrayDotNotation($testLines);
        
        /* The dump method must return the contents, no matter what they are. */
        $this->assertSame($data->dump(), $testLines);
    }

    public function testConstructorWithNull()
    {
        $data = new ArrayDotNotation(null);
        
        /* The dump method must return the contents, no matter what they are. */
        $this->assertSame($data->dump(), []);
    }
 
    public function testConstructorWithNothing()
    {
        $data = new ArrayDotNotation();
        
        /* The dump method must return the contents, no matter what they are. */
        $this->assertSame($data->dump(), []);
    }

    public function testSet()
    {
        $data = new ArrayDotNotation();
        $data->set('foo', 'Value for foo.');
        
        $this->assertSame([
            'foo' => 'Value for foo.'
        ], $data->dump());
    }

    public function testSetWithArrays()
    {
        $testLines = $this->getTestLines();
        $data = new ArrayDotNotation();
        $data->set('foo', $testLines[0]);
        $data->set('bar.abc', $testLines[1]);
        $data->set('bar.mno', $testLines[2]);
        $data->set('bar.xyz', $testLines[3]);
        $data->set('bar.other.baz', $testLines[4]);
        $data->set('deep.hole.went.to.go.test', $testLines[5]);
        
        $this->assertSame([
            'foo' => $testLines[0],
            'bar' => [
                'abc' => $testLines[1],
                'mno' => $testLines[2],
                'xyz' => $testLines[3],
                'other' => [
                    'baz' => $testLines[4]
                ]
            ],
            'deep' => [
                'hole' => [
                    'went' => [
                        'to' => [
                            'go' => [
                                'test' => $testLines[5]
                            ]
                        ]
                    ]
                ]
            ]
        ], $data->dump());
    }

}
