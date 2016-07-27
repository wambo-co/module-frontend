<?php

namespace Wambo\Frontend\Exception;

/**
 * Class ProductNotFoundException is used when a given product was not found.
 *
 * @package Wambo\Frontend\Exception
 */
class ProductNotFoundException extends \Exception
{
    /**
     * Creates a new instance of the ProductNotFoundException class.
     *
     * @param string $message An error message
     */
    public function __construct($message)
    {
        parent::__construct($message, 404);
    }
}