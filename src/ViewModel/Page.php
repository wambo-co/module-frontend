<?php

namespace Wambo\Frontend\ViewModel;

/**
 * Class Page is a view model which contains global, page-related fields.
 *
 * @package Wambo\Frontend\ViewModel
 */
class Page
{
    /** @var string $title The page title */
    public $title;

    /** @var string $description The page description */
    public $description;

    /** @var string $url The (canonical) page URL */
    public $url;
}