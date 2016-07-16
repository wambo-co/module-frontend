<?php

namespace Wambo\Frontend;

use Wambo\Core\App;
use Wambo\Core\Module\ModuleBootstrapInterface;

/**
 * Class Frontend integrates this module into Wambo application.
 *
 * @package Wambo\Frontend
 */
class Frontend implements ModuleBootstrapInterface
{
    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
    }
}
