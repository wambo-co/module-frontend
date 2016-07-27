<?php

namespace Wambo\Frontend\Orchestrator;

use Wambo\Frontend\ViewModel\Page;

/**
 * Class PageOrchestrator provides generic page view models.
 *
 * @package Wambo\Frontend\Orchestrator
 */
class PageOrchestrator
{
    /**
     * Get a global page view model model.
     *
     * @param string $title
     * @param string $description
     * @param string $slug
     *
     * @return Page
     */
    public function getPageModel(string $title, string $description = "", string $slug = ""): Page
    {
        $pageViewModel = new Page();
        $pageViewModel->title = $title;
        $pageViewModel->description = $description;
        $pageViewModel->url = $slug;

        return $pageViewModel;
    }
}