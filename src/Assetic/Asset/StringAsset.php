<?php

namespace Assetic\Asset;

use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a string asset.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class StringAsset implements AssetInterface
{
    private $loaded;
    private $filters;
    private $body;
    private $url;
    private $context;
    private $originalBody;

    /**
     * Constructor.
     *
     * @param string $body    The body of the asset
     * @param string $url     The asset URL
     * @param array  $filters Filters for the asset
     */
    public function __construct($body, $url = null, $filters = array())
    {
        $this->originalBody = $body;
        $this->url = $url;
        $this->filters = new FilterCollection($filters);
    }

    /** @inheritDoc */
    public function ensureFilter(FilterInterface $filter)
    {
        $this->filters->ensure($filter);
    }

    /** @inheritDoc */
    public function getFilters()
    {
        return $this->filters->all();
    }

    /** @inheritDoc */
    public function load(FilterInterface $additionalFilter = null)
    {
        $this->doLoad($this->originalBody, $additionalFilter);
    }

    /**
     * Loads the body of the current asset.
     *
     * @param string          $body             The asset body
     * @param FilterInterface $additionalFilter An additional filter
     */
    protected function doLoad($body, FilterInterface $additionalFilter = null)
    {
        $filter = clone $this->filters;
        if ($additionalFilter) {
            $filter->ensure($additionalFilter);
        }

        $asset = clone $this;
        $asset->setBody($body);

        $filter->filterLoad($asset);

        $this->setBody($asset->getBody());
        $this->loaded = true;
    }

    /** @inheritDoc */
    public function dump(FilterInterface $additionalFilter = null)
    {
        if (!$this->loaded) {
            $this->load();
        }

        $filter = clone $this->filters;
        if ($additionalFilter) {
            $filter->ensure($additionalFilter);
        }

        $asset = clone $this;
        $filter->filterDump($asset);

        return $asset->getBody();
    }

    /** @inheritDoc */
    public function getUrl()
    {
        return $this->url;
    }

    /** @inheritDoc */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /** @inheritDoc */
    public function getBody()
    {
        return $this->body;
    }

    /** @inheritDoc */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /** @inheritDoc */
    public function getContext()
    {
        return $this->context;
    }

    /** @inheritDoc */
    public function setContext(AssetInterface $context = null)
    {
        $this->context = $context;
    }
}