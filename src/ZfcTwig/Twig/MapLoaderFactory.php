<?php

namespace ZfcTwig\Twig;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class MapLoaderFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \ZfcTwig\moduleOptions $options */
        $options = $container->get('ZfcTwig\ModuleOptions');

        /** @var \Zend\View\Resolver\TemplateMapResolver */
        $zfTemplateMap = $container->get('ViewTemplateMapResolver');

        $templateMap = new MapLoader();
        foreach ($zfTemplateMap as $name => $path) {
            if ($options->getSuffix() == pathinfo($path, PATHINFO_EXTENSION)) {
                $templateMap->add($name, $path);
            }
        }

        return $templateMap;
    }
}