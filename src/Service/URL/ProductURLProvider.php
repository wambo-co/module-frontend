<?php

namespace Wambo\Frontend\Service\URL;

use Wambo\Catalog\Model\Slug;

/**
 * Class ProductURLProvider creates Product URLs.
 *
 * @package Wambo\Frontend\Service\URL
 */
class ProductURLProvider
{
    /**
     * Get the URL pattern
     *
     * @return string
     */
    public function getUrlPattern(): string
    {
        return "/product/{slug}";
    }

    /**
     * Get the URL prefix
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return "/product/";
    }

    /**
     * Get the URL for the given Slug.
     *
     * @param Slug $slug A product slug model
     *
     * @return string
     */
    public function getUrl(Slug $slug): string
    {
        return sprintf("%s%s", $this->getPrefix(), $slug->__toString());
    }
}