<?php

namespace Bmichotte\Dijkstra;

class Dijkstra
{
    private $positions;
    private $from;
    private $to;
    private $weights;
    private $predecessors;

    public function __construct(array $positions, Point $from, Point $to)
    {
        $this->positions = $positions;
        $this->from = $from;
        $this->to = $to;
    }

    public function findShortestPath(): array
    {
        $this->weights = [];
        $this->predecessors = [];

        foreach ($this->positions as $position) {
            $weight = -1;
            $passed = false;

            if ($position->equals($this->from)) {
                $weight = 0;
                $passed = true;
            }

            // init weights array
            $this->weights[$position->ref] = [
                'position' => $position,
                'weight' => $weight,
                'passed' => $passed,
            ];

            // init predecessors array
            $this->predecessors[$position->ref] = [
                'position' => $position,
                'previous' => null,
            ];
        }

        return $this->run($this->from)->getPath();
    }

    protected function run(Point $parent): self
    {
        // we reached the final point !
        if ($parent->equals($this->to)) {
            return $this;
        }

        // we can set this node has been passed by
        $this->weights[$parent->ref]['passed'] = true;

        // search for weight between $parent and its children
        foreach ($parent->points as $child) {
            $this->calculateWeight($parent, $child);
        }

        return $this->findNextParent();
    }

    protected function findNextParent(): self
    {
        // search for the next parent (smallest weight)
        // we have to find the smallest weight for a node we didn't passed by atm
        $smallest = INF;
        $nextParent = null;
        foreach ($this->weights as $weight) {
            if ($weight['weight'] < $smallest && $weight['weight'] !== -1 && ! $weight['passed']) {
                $smallest = $weight['weight'];
                $nextParent = $weight['position'];
            }
        }

        if ($nextParent !== null) {
            return $this->run($nextParent);
        }

        return $this;
    }

    protected function getPath(): array
    {
        $path = [$this->to];

        $point = $this->to;
        while (true) {
            foreach ($this->predecessors as $predecessor) {
                if ($predecessor['position']->equals($point)) {
                    $point = $predecessor['previous'];
                    $path[] = $point;

                    break;
                }
            }

            // $point is null -> path impossible
            if (is_null($point)) {
                unset($path[count($path) - 1]);
                break;
            }

            if ($point->equals($this->from)) {
                break;
            }
        }

        $path = array_reverse($path);

        return $path;
    }

    protected function calculateWeight(Point $parent, Point $child): void
    {
        /*
         * Dijkstra algo says :
         *
         * IF (child-node is not traversed yet) AND
         *    (WEIGHT(parent-node) + WEIGHT(DISTANCE(parent-node, child-node) < WEIGHT(child-node) OR WEIGHT(child-node) = -1)
         * THEN
         *    WEIGHT(child-node) = WEIGHT(parent-node) + WEIGHT(DISTANCE(parent-node, child-node)
         *  PREDECESSOR(child-node) = parent-node
         * ENDIF
         */
        if (! $this->weights[$child->ref]['passed']
            && ($this->weights[$parent->ref]['weight'] + static::distance($parent, $child) < $this->weights[$child->ref]['weight']
                || $this->weights[$child->ref]['weight'] === -1)) {
            $this->weights[$child->ref]['weight'] = $this->weights[$parent->ref]['weight'] + static::distance($parent, $child);
            $this->predecessors[$child->ref]['previous'] = $parent;
        }
    }

    public static function distance(Point $p1, Point $p2): float
    {
        // distance = square root of ((x2 - x1)^2 + (y2 - y1)^2)
        return sqrt(bcpow($p2->x - $p1->x, 2) + bcpow($p2->y - $p1->y, 2));
    }
}
