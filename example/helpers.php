<?php

use Bmichotte\Dijkstra\Point;
use Bmichotte\Dijkstra\Dijkstra;

function findLink(int $minDistance, array &$positions): void
{
    foreach ($positions as $point) {
        findLinkBetween($minDistance, $point, $positions);
    }
}

function findLinkBetween(int $minDistance, Point &$point1, array &$positions): void
{
    foreach ($positions as $point2) {
        if ($point1->equals($point2)) {
            continue;
        }

        $distance = Dijkstra::distance($point1, $point2);
        if ($distance < $minDistance) {
            $point1->addPoint($point2);
        }
    }

    if (0 === count($point1->points)) {
        findLinkBetween($minDistance * 2, $point1, $positions);
    }
}

function findFromTo(array $positions): array
{
    $from = null;
    $to = null;
    foreach ($positions as $point) {
        $from = $from ?: $point;
        $to = $to ?: $point;

        if ($point->x < $from->x && $point->y < $from->y) {
            $from = $point;
        }

        if ($point->x > $to->x && $point->y > $to->y) {
            $to = $point;
        }
    }

    return [$from, $to];
}

function drawPaths(int $max, array $positions, Point $from, Point $to, array $shortestPath, string $filename): void
{
    // open background
    $image = imagecreatetruecolor($max, $max);
    if ($image === false) {
        throw new Exception('Can not create image');
    }
    $color = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $color);

    // first run, draw lines
    $color = imagecolorallocate($image, 32, 230, 200);
    foreach ($positions as $point) {
        foreach ($point->points as $link) {
            drawLine($image, $point, $link, $color);
        }
    }

    // then, draw the points
    $color = imagecolorallocate($image, 32, 230, 36);
    foreach ($positions as $point) {
        imagefilledellipse($image, $point->x, $point->y, 10, 10, $color);
    }

    // draw the shortest path
    $color = imagecolorallocate($image, 255, 0, 255);
    $shortestPathLength = count($shortestPath);
    for ($i = 0; $i < $shortestPathLength; $i++) {
        $p = $shortestPath[$i];
        if (isset($shortestPath[$i + 1])) {
            $d = $shortestPath[$i + 1];
            drawLine($image, $p, $d, $color, 3);
        }
    }

    // and finally, draw the from and to points
    $color = imagecolorallocate($image, 255, 0, 255);
    imagefilledellipse($image, $from->x, $from->y, 10, 10, $color);
    imagefilledellipse($image, $to->x, $to->y, 10, 10, $color);

    imagepng($image, $filename);
    imagedestroy($image);
}

function drawLine($image, Point $point1, Point $point2, $color, int $thick = 1): void
{
    if (null === $point1 || null === $point2) {
        return;
    }
    if (null === $point1->x || null === $point1->y) {
        return;
    }
    if (null === $point2->x || null === $point2->y) {
        return;
    }

    if ($point1->x === $point2->x) {
        $from = $point1->y < $point2->y ? $point1 : $point2;
        $to = $point1->y > $point2->y ? $point1 : $point2;
    } else {
        $from = $point1->x < $point2->x ? $point1 : $point2;
        $to = $point1->x > $point2->x ? $point1 : $point2;
    }

    imagesetthickness($image, $thick);

    imageline($image, $from->x, $from->y, $to->x, $to->y, $color);
}
