<?php

namespace ZfcTwig\Twig;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class StackLoaderFactory implements FactoryInterface
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

        /** @var $templateStack \Zend\View\Resolver\TemplatePathStack */
        $zfTemplateStack = $container->get('ViewTemplatePathStack');

        $templateStack = new StackLoader($zfTemplateStack->getPaths()->toArray());
        $templateStack->setDefaultSuffix($options->getSuffix());

        return $templateStack;
    }
}
