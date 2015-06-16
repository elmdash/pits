<?php

namespace Peach\Tests\Support;

use Peach\Support\Path;

class PathTest extends \PHPUnit_Framework_TestCase
{


    public function makeProvider()
    {
        $full = 'some/dir/file.xml';
        return [
          ['file.xml', ['file', 'xml']],
          [$full, ['some', 'dir', 'file', 'xml']],
          [$full, [['some', 'dir'], 'file', 'xml']],
          [$full, [['some', 'dir', 'file'], 'xml']],
          [$full, [['some', 'dir/file'], 'xml']],
          [$full, ['some', 'dir', 'file.xml']],
          ['file.xml', ['file.xml']],
          ['', []],
        ];
    }


    public function joinProvider()
    {
        $full = 'test/one/two/three';
        return [
          [$full, ['test', 'one', 'two', 'three']],
          [$full, ['test/one', ['two', 'three']]],
          ['/' . $full, ['/test/one/two', 'three']],
          ['/' . $full . '/', ['/test/one/two', 'three/']],
          ['', []],
          ['/', ['/']],
          [$full, [['test', 'one', 'two', 'three']]],
          [$full, [['test', 'one', ['two', 'three']]]],
          [$full, ['test/', '/one/', '/two//', '/three']],
          ['/' . $full . '/', ['/test/', '/one/', '/two//', '/three/']],
        ];
    }

    /**
     * @test
     * @dataProvider makeProvider
     * @param string $expected
     * @param array $args
     */
    public function itMakesPaths($expected, $args)
    {
        $this->assertEquals(
          $expected,
          call_user_func_array([Path::class, 'make'], $args)
        );
    }


    /**
     * @test
     * @dataProvider joinProvider
     * @param string $expected
     * @param array $args
     */
    public function itJoinsPaths($expected, $args)
    {
        $this->assertEquals(
          $expected,
          call_user_func_array([Path::class, 'join'], $args)
        );
    }
}