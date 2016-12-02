<?php

namespace ZfcTwig\Twig;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use InvalidArgumentException;
use Twig_Loader_Chain;
use Zend\ServiceManager\Factory\FactoryInterface;

class ChainLoaderFactory implements FactoryInterface
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

        // Setup loader
        $chain = new Twig_Loader_Chain();

        foreach ($options->getLoaderChain() as $loader) {
            if (!is_string($loader) || !$container->has($loader)) {
                throw new InvalidArgumentException('Loaders should be a service manager alias.');
            }
            $chain->addLoader($container->get($loader));
        }

        return $chain;
    }
}