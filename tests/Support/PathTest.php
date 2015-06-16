<?php

namespace Peach\Tests\Support;

use Peach\Support\Path;

class PathTest extends \PHPUnit_Framework_TestCase {


    /** @test */
    public function itMakesPathFromArgsVar1() {
        $res = Path::make('some','dir','file','xml');
        $this->assertEquals('some/dir/file.xml', $res);
    }

    /** @test */
    public function itMakesPathFromArgsVar2() {
        $res = Path::make('file','xml');
        $this->assertEquals('file.xml', $res);
    }

    /** @test */
    public function itMakesPathFromSomeArrayArgsVar1() {
        $res = Path::make(['some','dir'],'file','xml');
        $this->assertEquals('some/dir/file.xml', $res);
    }

    /** @test */
    public function itMakesPathFromSomeArrayArgsVar2() {
        $res = Path::make(['some','dir','file'],'xml');
        $this->assertEquals('some/dir/file.xml', $res);
    }

    /** @test */
    public function itMakesPathFromSomeArrayArgsVar3() {
        $res = Path::make(['some','dir/file'],'xml');
        $this->assertEquals('some/dir/file.xml', $res);
    }

    /** @test */
    public function itMakesPathFromSomeArrayArgsVar4() {
        $res = Path::make('some','dir','file.xml');
        $this->assertEquals('some/dir/file.xml', $res);
    }

}