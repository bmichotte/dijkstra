<?php

use Bmichotte\Dijkstra\Point;
use Bmichotte\Dijkstra\Dijkstra;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/helpers.php';

// number of nodes
$nodes = 150;

// maximum size of the map
$max = 800;

// distance between two nodes
$minDistance = 120;

// we add "$nodes" dynamic nodes
$positions = [];
foreach (range(0, $nodes) as $value) {
    $positions[] = new Point(rand(0, $max), rand(0, $max));
}

// add random links
findLink($minDistance, $positions);

// find the most distant points
list($from, $to) = findFromTo($positions);

$dijkstra = new Dijkstra($positions, $from, $to);
$shortestPath = $dijkstra->findShortestPath();

// draw the result
drawPaths($max, $positions, $from, $to, $shortestPath, 'image.png');
