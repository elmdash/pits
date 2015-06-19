<?php

namespace Peach\Tests\Support;

use Peach\Support\Num;

class NumTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itDetectsEvens()
    {
        $this->assertTrue(Num::isEven(0));
        $this->assertTrue(Num::isEven(2));
        $this->assertFalse(Num::isEven(3));
        $this->assertTrue(Num::isEven(33333390));
        $this->assertTrue(Num::isEven(33333392));
    }

    /** @test */
    public function itDetectsOdds()
    {
        $this->assertFalse(Num::isOdd(0));
        $this->assertTrue(Num::isOdd(1));
        $this->assertFalse(Num::isOdd(2));
        $this->assertFalse(Num::isOdd(33333390));
        $this->assertTrue(Num::isOdd(3333339));
    }

}