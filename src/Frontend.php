<?php

namespace Wambo\Frontend;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Slim\Views\Twig;
use Wambo\Catalog\CachedProductRepository;
use Wambo\Catalog\Mapper\ContentMapper;
use Wambo\Catalog\Mapper\ProductMapper;
use Wambo\Catalog\ProductRepository;
use Wambo\Catalog\ProductRepositoryInterface;
use Wambo\Core\App;
use Wambo\Core\Module\JSONModuleStorage;
use Wambo\Core\Module\ModuleBootstrapInterface;
use Stash\Pool;
use Wambo\Frontend\Controller\ErrorController;

/**
 * Class Frontend registers the frontend controller in the Wambo app.
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
        // Get container
        $container = $app->getContainer();

        // register: renderer
        $container['renderer'] = function ($c) {

            $templatesDirectory = realpath(dirname(__FILE__) . '/../view');
            $cacheDirectory = realpath(WAMBO_ROOT_DIR . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "cache");

            $view = new Twig($templatesDirectory, [
                'cache' => $cacheDirectory
            ]);

            // Instantiate and add Slim specific extension
            $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
            $view->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));

            return $view;
        };

        // register: product repository
        $container["productRepository"] = $this->getProductRepository();

        // register: error controller
        $errorController = new ErrorController($container);
        $container['errorController'] = $errorController;
        $container['notFoundHandler'] = function () use ($errorController) {
            return function ($request, $response, $args) use ($errorController) {
                return $errorController->error404($request, $response, $args);
            };
        };

        // overview
        $app->get('/', 'Wambo\Frontend\Controller\CatalogController:overview');

        // product details
        $app->get('/product/{slug}', 'Wambo\Frontend\Controller\CatalogController:productDetails');
    }

    /**
     * Get a product repository instance
     *
     * @return ProductRepositoryInterface
     */
    private function getProductRepository()
    {
        // catalog storage
        $sampleCatalogFilename = "sample-catalog.json";
        $testResourceFolderPath = realpath(WAMBO_ROOT_DIR . "/vendor/wambo/module-catalog/examples/" . '/catalog');

        $localFilesystemAdapter = new Local($testResourceFolderPath);
        $filesystem = new Filesystem($localFilesystemAdapter);
        $storage = new JSONModuleStorage($filesystem, $sampleCatalogFilename);

        // product mapper
        $contentMapper = new ContentMapper();
        $productMapper = new ProductMapper($contentMapper);

        // create the product repository
        $productRepository = new ProductRepository($storage, $productMapper);

        // create a cached version of the product repository
        $cache = new Pool();
        $cachedProductRepository = new CachedProductRepository($cache, $productRepository);
        return $cachedProductRepository;
    }
}
