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
class Dijkstra
{
	/**
	 * The positions of nodes
	 * @var array
	 */
	private $positions;
	
	/**
	 * The starting point
	 * @var Point
	 */
	private $from;
	
	/**
	 * The ending point
	 * @var Point
	 */
	private $to;
	
	/**
	 * Array used to save the weights between nodes
	 * @var array
	 */
	private $weights;
	
	/**
	 * Array used to save the predecessor of nodes
	 * @var array
	 */
	private $predecessors;
	
	/**
	 *
	 * @param array $positions
	 * @param Point $from
	 * @param Point $to 
	 */
	public function __construct(array $positions, Point $from, Point $to)
	{
		$this->positions = $positions;
		$this->from = $from;
		$this->to = $to;
	}
	
	/**
	 * Find the shortest path between $this->from and $this->to
	 * @return array 
	 */
	public function findShortestPath()
	{
		$this->weights = array();
		$this->predecessors = array();
		
		foreach ($this->positions as $position)
		{
			$weight = -1;
			$passed = false;
			
			if ($position->equal($this->from))
			{
				$weight = 0;
				$passed = true;
			}
			
			// init weights array
			$this->weights[$position->getRef()] = array(
				'position' => $position,
				'weight' => $weight,
				'passed' => $passed
			);
			
			// init predecessors array
			$this->predecessors[$position->getRef()] = array(
				'position' => $position,
				'previous' => null
			);
		}
		
		$this->run($this->from);
	
		return $this->getPath();
	}
	
	/**
	 * Find the shortest path between a node and its linked nodes
	 * @param Point $parent
	 * @return void
	 */
	protected function run(Point $parent)
	{
		// we reached the final point !
		if ($parent->equal($this->to))
		{
			return;
		}
		
		// we can set this node has been passed by
		$this->weights[$parent->getRef()]['passed'] = true;
		
		// search for weight between $parent and its children
		foreach ($parent->getPoints() as $child)
		{
			$this->calculateWeight($parent, $child);
		}
		
		// search for the next parent (smallest weight)
		// we have to find the smallest weight for a node we didn't passed by atm
		$smallest = INF;
		$nextParent = null;
		foreach ($this->weights as $weight)
		{
			if ($weight['weight'] < $smallest && $weight['weight'] != -1 && ! $weight['passed'])
			{
				$smallest = $weight['weight'];
				$nextParent = $weight['position'];
			}
		}
		if (!is_null($nextParent))
		{
			$this->run($nextParent);
		}
	}
	
	/**
	 * Return the path (array of Point) between $this->from and $this->to.
	 * <br />Warning, the result can be incomplete (no path found)
	 * @return array
	 */
	protected function getPath()
	{
		$path = array($this->to);
		
		$point = $this->to;
		while (true)
		{
			foreach ($this->predecessors as $predecessor)
			{
				if ($predecessor['position']->equal($point))
				{
					$point  = $predecessor['previous'];
					$path[] = $point;
					
					break;
				}
			}
			
			// $point is null -> path impossible
			if (is_null($point))
			{
				unset($path[count($path) - 1]);
				break;
			}
			
			if ($point->equal($this->from))
			{
				break;
			}
		}
		
		$path = array_reverse($path);
		return $path;
	}
	
	/**
	 * Search for the weight of a path between two nodes.
	 * @link http://en.wikipedia.org/wiki/Dijkstra%27s_algorithm For more info about the algorithm
	 * @param Point $parent
	 * @param Point $child 
	 * @return void
	 */
	protected function calculateWeight(Point $parent, Point $child)
	{
		/*
		 * Dijkstra algo says :
		 * 
		 * IF (child-node is not traversed yet) AND 
		 *	(WEIGHT(parent-node) + WEIGHT(DISTANCE(parent-node, child-node) < WEIGHT(child-node) OR WEIGHT(child-node) = -1)
		 * THEN
		 *	WEIGHT(child-node) = WEIGHT(parent-node) + WEIGHT(DISTANCE(parent-node, child-node)
		 *  PREDECESSOR(child-node) = parent-node
		 * ENDIF
		 */
		if (! $this->weights[$child->getRef()]['passed'] 
				&& ($this->weights[$parent->getRef()]['weight'] + $this->distance($parent, $child) < $this->weights[$child->getRef()]['weight']
					|| $this->weights[$child->getRef()]['weight'] == -1))
		{
			$this->weights[$child->getRef()]['weight'] = $this->weights[$parent->getRef()]['weight'] + $this->distance($parent, $child);
			$this->predecessors[$child->getRef()]['previous'] = $parent;
		}
	}
	
	/**
	 * Find the distance between 2 points
	 * @var Point $p1
	 * @var Point $p2
	 * @return double
	 */
	protected function distance(Point $p1, Point $p2)
	{
		// distance = square root of ((x2 - x1)^2 + (y2 - y1)^2)
		$distance = sqrt(bcpow($p2->getX() - $p1->getX(), 2) + bcpow($p2->getY() - $p1->getY(), 2));
		return $distance;
	}
}

