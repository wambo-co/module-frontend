<?php

namespace Wambo\Frontend\Service\URL;

/**
 * Class GenericURLProvider creates generic URLs for Wambo apps.
 *
 * @package Wambo\Frontend\Service\URL
 */
class GenericURLProvider
{
    /**
     * Get the URL pattern
     *
     * @return string
     */
    public function getUrlPattern(): string
    {
        return "/";
    }

    /**
     * Get the URL prefix
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return "/";
    }

    /**
     * Get the URL for the given path.
     *
     * @return string
     */
    public function getUrl(string $path): string
    {
        $cleanedPath = $this->stripLeadingSlashes($path);
        if (strlen($cleanedPath) == 0) {
            return $this->getPrefix();
        }

        return sprintf("%s%s", $this->getPrefix(), $cleanedPath);
    }

    /**
     * Remove leading slashed from a given path
     *
     * @param string $path A path (e.g. "/product/sample-product-01")
     *
     * @return string
     */
    private function stripLeadingSlashes(string $path): string
    {
        return preg_replace('/^\/+/', '', $path);
    }
}