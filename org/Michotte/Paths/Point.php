<?php

namespace org\Michotte\Paths;

/**
 * php5 implementation of Dijkstra algorithm
 * @link http://en.wikipedia.org/wiki/Dijkstra%27s_algorithm 
 *
 * @author Benjamin Michotte <bmichotte@gmail.com>
 *
 * Copyright (c) 2012, Benjamin Michotte
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the University of California, Berkeley nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE REGENTS AND CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class Point
{
	/**
	 * @var float $x The X coordinate of the Point
	 */
	private $x;

	/**
	 * @var float $y The y coordinate of the Point
	 */
	private $y;

	/**
	 * @var Point[] $points The "Point"s linked to this Point
	 */
	private $points;

	public function __construct()
	{
		$this->points = array();
	}

	/**
	 * Set the X coordinate
	 *
	 * @param float $x
	 * @return Point
	 */
	public function setX($x)
	{
		$this->x = $x;
		return $this;
	}

	/**
	 * Get the X coordinate
	 *
	 * @return float 
	 */
	public function getX()
	{
		return $this->x;
	}

	/**
	 * Set the Y coordinate
	 *
	 * @param float $y
	 * @return Point
	 */
	public function setY($y)
	{
		$this->y = $y;
		return $this;
	}

	/**
	 * Get the Y coordinate
	 *
	 * @return float 
	 */
	public function getY()
	{
		return $this->y;
	}

	/**
	 * Add link
	 *
	 * @param Point $point
	 * @return Point
	 */
	public function addPoint(Point $point)
	{
		if (!in_array($point, $this->points))
		{
			$this->points[] = $point;
		}
		
		// add the reverse point
		if (!in_array($this, $point->points))
		{
			$point->points[] = $this;
		}
		return $this;
	}

	/**
	 * Get links
	 * 
	 * @return array
	 */
	public function getPoints()
	{
		return $this->points;
	}

	/**
	 * Identify the Point by the concatenation of x and y
	 * @return string
	 */
	public function getRef()
	{
		return $this->getX() . '-' . $this->getY();
	}
	
	/**
	 * Simple equal function
	 * @param Point $point
	 * @return boolean 
	 */
	public function equal($point)
	{
		if (! ($point instanceof Point))
		{
			return false;
		}
		return $this->getRef() == $point->getRef();
	}
}