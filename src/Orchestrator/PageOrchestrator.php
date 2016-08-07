<?php

namespace Wambo\Frontend\Orchestrator;

use Wambo\Frontend\Service\URL\GenericURLProvider;
use Wambo\Frontend\ViewModel\Page;

/**
 * Class PageOrchestrator provides generic page view models.
 *
 * @package Wambo\Frontend\Orchestrator
 */
class PageOrchestrator
{
    /**
     * @var GenericURLProvider
     */
    private $genericURLProvider;

    /**
     * Create a new PageOrchestrator instance.
     *
     * @param GenericURLProvider $genericURLProvider An URL provider
     */
    public function __construct(GenericURLProvider $genericURLProvider)
    {
        $this->genericURLProvider = $genericURLProvider;
    }

    /**
     * Get a global page view model model.
     *
     * @param string $title
     * @param string $description
     * @param string $path
     *
     * @return Page
     */
    public function getPageModel(string $title, string $description = "", string $path = ""): Page
    {
        $pageViewModel = new Page();
        $pageViewModel->title = $title;
        $pageViewModel->description = $description;
        $pageViewModel->url = $this->genericURLProvider->getUrl($path);

        return $pageViewModel;
    }
}