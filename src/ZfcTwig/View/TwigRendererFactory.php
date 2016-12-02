<?php

namespace ZfcTwig\View;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class TwigRendererFactory implements FactoryInterface
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

        $renderer = new TwigRenderer(
            $container->get('Zend\View\View'),
            $container->get('Twig_Loader_Chain'),
            $container->get('Twig_Environment'),
            $container->get('ZfcTwig\View\TwigResolver')
        );

        $renderer->setCanRenderTrees($options->getDisableZfmodel());
        $renderer->setHelperPluginManager($container->get('ZfcTwigViewHelperManager'));

        return $renderer;
    }
}