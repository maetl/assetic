<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset\Iterator;

use Assetic\Asset\AssetCollectionInterface;

/**
 * Iterates over an asset collection.
 *
 * The iterator is responsible for cascading filters and target URL patterns
 * from parent to child assets.
 *
 * It provides the ability to mantain a 1 - 1 mapping to request to filesystem
 *
 * @author Jared Wyles <Jared.wyles@bigcommerce.com>
 */
class DebugAssetCollectionIterator implements \RecursiveIterator
{
	private $assets;
	private $filters;
	private $output;
	private $clones;

	public function __construct(AssetCollectionInterface $coll, \SplObjectStorage $clones)
	{
		$this->assets  = $coll->all();
		$this->filters = $coll->getFilters();
		$this->output  = $coll->getTargetPath();
		$this->clones  = $clones;

		if (false === $pos = strpos($this->output, '.')) {
			$this->output .= '_*';
		} else {
			$this->output = substr($this->output, 0, $pos).'_*'.substr($this->output, $pos);
		}
	}

	/**
	 * Returns a copy of the current asset with filters and a target URL applied.
	 *
	 * @param Boolean $raw Returns the unmodified asset if true
	 */
	public function current($raw = false)
	{
		$asset = current($this->assets);
		$asset->setTargetPath($asset->getSourcePath());
		return $asset;
	}

	public function key()
	{
		return key($this->assets);
	}

	public function next()
	{
		return next($this->assets);
	}

	public function rewind()
	{
		return reset($this->assets);
	}

	public function valid()
	{
		return false !== current($this->assets);
	}

	public function hasChildren()
	{
		return current($this->assets) instanceof AssetCollectionInterface;
	}

	/**
	 * @uses current()
	 */
	public function getChildren()
	{
		return new self($this->current(), $this->clones);
	}
}
