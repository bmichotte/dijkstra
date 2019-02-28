<?php

namespace Bmichotte\Dijkstra\Tests;

use Bmichotte\Dijkstra\Point;
use PHPUnit\Framework\TestCase;
use Bmichotte\Dijkstra\Dijkstra;

class DijkstraTest extends TestCase
{
    /** @test */
    public function it_should_find_a_path()
    {
        $p1 = new Point(1, 1);
        $p2 = new Point(2, 2);
        $p1->addPoint($p2);

        $dijkstra = new Dijkstra([$p1, $p2], $p1, $p2);
        $path = $dijkstra->findShortestPath();

        $this->assertCount(2, $path);
        $this->assertTrue($path[0]->equals($p1));
        $this->assertTrue($path[1]->equals($p2));
    }

    /** @test */
    public function it_should_find_a_short_path()
    {
        $p1 = new Point(1, 1);
        $p2 = new Point(2, 2);
        $p3 = new Point(3, 8);
        $p4 = new Point(8, 2);
        $p5 = new Point(10, 10);

        $p1->addPoint($p2);

        $p2->addPoint($p3);
        $p2->addPoint($p4);

        $p3->addPoint($p4);
        $p4->addPoint($p5);

        $dijkstra = new Dijkstra([$p1, $p2, $p3, $p4, $p5], $p1, $p5);
        $path = $dijkstra->findShortestPath();

        $this->assertCount(4, $path);
        $this->assertTrue($path[0]->equals($p1));
        $this->assertTrue($path[1]->equals($p2));
        $this->assertTrue($path[2]->equals($p4));
        $this->assertTrue($path[3]->equals($p5));
    }

    /** @test */
    public function it_could_have_an_impossible_path()
    {
        $p1 = new Point(1, 1);
        $p2 = new Point(2, 2);

        $dijkstra = new Dijkstra([$p1, $p2], $p1, $p2);
        $path = $dijkstra->findShortestPath();
        $this->assertCount(1, $path);
    }
}
