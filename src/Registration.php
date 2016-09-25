<?php

namespace Wambo\Frontend;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Wambo\Core\App;
use Wambo\Core\Module\ModuleBootstrapInterface;
use Wambo\Frontend\Controller\CartController;
use Wambo\Frontend\Controller\CatalogController;
use Wambo\Frontend\Controller\ErrorController;
use Wambo\Frontend\Service\URL\GenericURLProvider;
use Wambo\Frontend\Service\URL\ProductURLProvider;

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
        /** @var \Wambo\Frontend\Service\URL\GenericURLProvider $genericURLProvider */
        $genericURLProvider = $app->getContainer()->get(GenericURLProvider::class);
        $app->get($genericURLProvider->getUrlPattern(), ['CatalogController', 'overview']);

        // product details
        /** @var \Wambo\Frontend\Service\URL\ProductURLProvider $productURLProvider */
        $productURLProvider = $app->getContainer()->get(ProductURLProvider::class);
        $app->get($productURLProvider->getUrlPattern(), ['CatalogController', 'productDetails']);

        // cart
        $app->get('/cart', ['CartController', 'index']);
        $app->post('/cart', ['CartController', 'index']);

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
            /** @var Request $request */
            $request = $container->get('request');

            /** @var Uri $requestUri */
            $requestUri = $request->getUri();

            $basePath = rtrim(str_ireplace('index.php', '', $requestUri->getBasePath()), '/');

            $view->addExtension(new TwigExtension($container->get('router'), $basePath));

            return $view;
        });

        // register: error controller
        $container->set('CatalogController', \DI\object(CatalogController::class));
        $container->set('CartController', \Di\object(CartController::class));
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
