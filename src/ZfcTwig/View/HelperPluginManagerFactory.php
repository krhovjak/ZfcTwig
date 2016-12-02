<?php

namespace ZfcTwig\View;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Exception;

class HelperPluginManagerFactory implements FactoryInterface
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
        $options        = $container->get('ZfcTwig\ModuleOptions');
        $managerOptions = $options->getHelperManager();
        $managerConfigs = isset($managerOptions['configs']) ? $managerOptions['configs'] : array();

        $baseManager = $container->get('ViewHelperManager');
        $twigManager = new HelperPluginManager(new Config($managerOptions));
        $twigManager->setServiceLocator($container);
        $twigManager->addPeeringServiceManager($baseManager);

        foreach ($managerConfigs as $configClass) {
            if (is_string($configClass) && class_exists($configClass)) {
                $config = new $configClass;

                if (!$config instanceof ConfigInterface) {
                    throw new Exception\RuntimeException(
                        sprintf(
                            'Invalid service manager configuration class provided; received "%s",
                                expected class implementing %s',
                            $configClass,
                            'Zend\ServiceManager\ConfigInterface'
                        )
                    );
                }

                $config->configureServiceManager($twigManager);
            }
        }

        return $twigManager;
    }
}
