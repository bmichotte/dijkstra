# Dijkstra algorithm implementation

[![Build Status](https://img.shields.io/travis/bmichotte/dijkstra/master.svg?style=flat-square)](https://travis-ci.org/bmichotte/dijkstra)
[![Quality Score](https://img.shields.io/scrutinizer/g/bmichotte/dijkstra.svg?style=flat-square)](https://scrutinizer-ci.com/g/bmichotte/dijkstra)

More on the algorithm : https://en.wikipedia.org/wiki/Dijkstra%27s_algorithm

You can find an example on the `example` directory. The result of this example should be something like
![shortest paths](https://github.com/bmichotte/dijkstra/blob/master/example/image.png)

## Installation

Run `composer require bmichotte/dijkstra`

## Usage

```php
// create some points
$point1 = new \Bmichotte\Dijkstra\Point(/* x */ 1, /* y */ 1);
$point2 = new \Bmichotte\Dijkstra\Point(2, 2);
$point3 = new \Bmichotte\Dijkstra\Point(3, 3);

$all_points = [$point1, $point2, $point3];

// "join" them
$point1->addPoint($point2);
$point1->addPoint($point3);
$point2->addPoint($point3);

// find the shortest path
$dijkstra = new \Bmichotte\Dijkstra\Dijkstra($all_points, /* from */ $point1, /* to */ $point3);
$shortest_path = $dijkstra->findShortestPath();

// $shortest_path[0] == $point1
// $shortest_path[1] == $point3
```
