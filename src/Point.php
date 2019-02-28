<?php

namespace Bmichotte\Dijkstra;

class Point
{
    public $x;
    public $y;
    public $points;
    public $ref;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->points = [];
        $this->ref = "{$x}-{$y}";
    }

    public function addPoint(self $point): self
    {
        if (! in_array($point, $this->points)) {
            $this->points[] = $point;
        }

        // add the reverse point
        if (! in_array($this, $point->points)) {
            $point->points[] = $this;
        }

        return $this;
    }

    public function equals(self $point): bool
    {
        return $this->ref === $point->ref;
    }
}
