<?php

namespace Wambo\Frontend;

use Interop\Container\ContainerInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Http\Message\RequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Wambo\Catalog\CachedProductRepository;
use Wambo\Catalog\Mapper\ContentMapper;
use Wambo\Catalog\Mapper\ProductMapper;
use Wambo\Catalog\ProductRepository;
use Wambo\Catalog\ProductRepositoryInterface;
use Wambo\Core\App;
use Wambo\Core\Module\JSONModuleStorage;
use Wambo\Core\Module\ModuleBootstrapInterface;
use Stash\Pool;
use Wambo\Frontend\Controller\CatalogController;
use Wambo\Frontend\Controller\ErrorController;

/**
 * Class Registration registers the frontend module in the Wambo app.
 *
 * @package Wambo\Frontend
 */
class Registration implements ModuleBootstrapInterface
{
    /**
     * Register the Frontend module.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->configureDI($app);
        $this->registerRoutes($app);
    }

    /**
     * Register routes in the slim app.
     *
     * @param App $app
     */
    private function registerRoutes(App $app)
    {
        // overview
        $app->get('/', ['CatalogController', 'overview']);

        // product details
        $app->get('/product/{slug}', ['CatalogController', 'productDetails']);
    }

    /**
     * Configure the dependency injection container
     *
     * @param App $app
     */
    private function configureDI(App $app)
    {
        /** @var \DI\Container $container */
        $container = $app->getContainer();

        // register: renderer
        $container->set(Twig::class, function (ContainerInterface $container) {

            $templatesDirectory = realpath(dirname(__FILE__) . '/../view');
            $cacheDirectory = realpath(WAMBO_ROOT_DIR . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "cache");

            $view = new Twig($templatesDirectory, [
                'cache' => $cacheDirectory
            ]);

            // Instantiate and add Slim specific extension
            /** @var RequestInterface $request */
            $request = $container->get('request');
            $basePath = rtrim(str_ireplace('index.php', '', $request->getUri()->getBasePath()), '/');
            $view->addExtension(new TwigExtension($container->get('router'), $basePath));

            return $view;
        });

        // register: error controller
        $container->set('CatalogController', \DI\object(CatalogController::class));
        $container->set('errorController', \DI\object(ErrorController::class));
        $container->set('notFoundHandler', function (ContainerInterface $container) {
            return function (Request $request, Response $response) use ($container) {
                /** @var ErrorController $errorController */
                $errorController = $container->get("errorController");
                return $errorController->error404($request, $response);
            };
        });
    }
}
