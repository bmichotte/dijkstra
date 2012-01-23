<?php

require 'org/Michotte/Paths/Dijkstra.php';
require 'org/Michotte/Paths/Point.php';

use \org\Michotte\Paths\Dijkstra;
use \org\Michotte\Paths\Point;

// number of nodes
$nodes = 90;

// maximum size of the map
$max = 800;

// distance between two nodes
$minDistance = 150;

// we add $nodes dynamic nodes
$positions = array();
for ($i = 0; $i < $nodes; $i++)
{
	$p = new Point();
	$p->setX(rand(0, $max));
	$p->setY(rand(0, $max));
	$positions[] = $p;
}

// add links
findLink($minDistance, $positions);

list ($from, $to) = findFromTo($positions);

$dijkstra = new Dijkstra($positions, $from, $to);
$shortestPath = $dijkstra->findShortestPath();

drawPaths($max, $positions, $from, $to, $shortestPath);

/**
 * Add links between the points
 * WARNING, this method doesn't work all the time
 * 
 * @param type $minDistance
 * @param array $positions 
 */
function findLink($minDistance, array &$positions)
{
	for ($i = 0; $i < count($positions); $i++)
	{
		$p1 = $positions[$i];
		findLinkBetween($minDistance, $p1, $positions);
	}
}

/**
 * Add a link between two points if they are at a distance less than $distance.
 * This function is recursive with $minDistance *= 2 if no link are found
 * 
 * @param int $minDistance
 * @param Point $p1
 * @param array $positions 
 */
function findLinkBetween($minDistance, Point &$p1, array &$positions)
{
	for ($o = 0; $o < count($positions); $o++)
	{
		$p2 = $positions[$o];
		if ($p1->equal($p2))
		{
			continue;
		}

		$distance = distance($p1, $p2);
		if ($distance < $minDistance)
		{
			$p1->addPoint($p2);
		}
	}

	if (0 == count($p1->getPoints()))
	{
		findLinkBetween($minDistance * 2, $p1, $positions);
	}
}

/**
 * Find the distance between two points
 * @param Point $p1
 * @param Point $p2
 * @return float
 */
function distance(Point $p1, Point $p2)
{
	// distance = square root of ((x2 - x1)^2 + (y2 - y1)^2)
	$distance = sqrt(bcpow($p2->getX() - $p1->getX(), 2) + bcpow($p2->getY() - $p1->getY(), 2));
	return $distance;
}

/**
 * Find the "from" and "to" points
 * from = most left-upper point
 * to = most rigth-lower point
 * 
 * @param array $positions
 * @return array
 */
function findFromTo(array $positions)
{
	$from = null;
	$to = null;
	foreach ($positions as $p)
	{
		if (is_null($from))
		{
			$from = $p;
		}
		if (is_null($to))
		{
			$to = $p;
		}

		if ($p->getX() < $from->getX() && $p->getY() < $from->getY())
		{
			$from = $p;
		}

		if ($p->getX() > $to->getX() && $p->getY() > $to->getY())
		{
			$to = $p;
		}
	}
	
	return array($from, $to);
}

/**
 * Draw the result
 * @param int $max
 * @param array $positions
 * @param Point $from
 * @param Point $to
 * @param array $shortestPath 
 */
function drawPaths($max, array $positions, Point $from, Point $to, array $shortestPath)
{
	// open background
	$image = imagecreatetruecolor($max, $max);
	$color = imagecolorallocate($image, 255, 255, 255);
	imagefill($image, 0, 0, $color);

	// first run, draw lines
	$color = imagecolorallocate($image, 32, 230, 200);
	foreach ($positions as $point)
	{
		foreach ($point->getPoints() as $link)
		{
			drawLine($image, $point, $link, $color);
		}
	}
		
	// then, draw the points
	$color = imagecolorallocate($image, 32, 230, 36);
	foreach ($positions as $point)
	{
		imagefilledellipse($image, $point->getX(), $point->getY(), 10, 10, $color);
	}
	
	// draw the shortest path
	$color = imagecolorallocate($image, 255, 0, 255);
	for ($i = 0; $i < count($shortestPath); $i++)
	{
		$p = $shortestPath[$i];
		if (isset($shortestPath[$i + 1]))
		{
			$d = $shortestPath[$i + 1];
			drawLine($image, $p, $d, $color, 3);
		}
	}
	
	// and finally, draw the from and to points
	$color = imagecolorallocate($image, 255, 0, 255);
	imagefilledellipse($image, $from->getX(), $from->getY(), 10, 10, $color);
	imagefilledellipse($image, $to->getX(), $to->getY(), 10, 10, $color);

	header("Content-type: image/png");
	imagepng($image);
	die();
}

/**
 * Draw a line
 * @param resource $image
 * @param Point $p Point 1
 * @param Point $d Point 2
 * @param int $color
 * @param int $thick
 * @return void
 */
function drawLine($image, Point $p, Point $d, $color, $thick = 1)
{
	if (is_null($p) || is_null($d) || is_null($p->getX()) || is_null($p->getY()) || is_null($d->getX()) || is_null($d->getY()))
	{
		return;
	}

	if ($p->getX() == $d->getX())
	{
		$from = $p->getY() < $d->getY() ? $p : $d;
		$to = $p->getY() > $d->getY() ? $p : $d;
	}
	else
	{
		$from = $p->getX() < $d->getX() ? $p : $d;
		$to = $p->getX() > $d->getX() ? $p : $d;
	}

	imagesetthickness($image, $thick);

	imageline($image, $from->getX(), $from->getY(), $to->getX(), $to->getY(), $color);
}