<?php

namespace Bmichotte\Dijkstra\Tests;

use Bmichotte\Dijkstra\Point;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    /** @test */
    public function it_should_have_a_correct_ref()
    {
        $point = new Point(10, 12);
        $this->assertEquals('10-12', $point->ref);
    }

    /** @test */
    public function it_should_add_a_point()
    {
        $point = new Point(10, 12);
        $point2 = new Point(18, 12);

        $point->addPoint($point2);

        $this->assertTrue(in_array($point2, $point->points));
    }

    /** @test */
    public function it_should_not_equals()
    {
        $point = new Point(10, 12);
        $point2 = new Point(18, 12);

        $this->assertFalse($point->equals($point2));
    }
}
