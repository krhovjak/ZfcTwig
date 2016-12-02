<?php

namespace ZfcTwig\Twig;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use RuntimeException;
use Twig_Environment;
use Zend\ServiceManager\Factory\FactoryInterface;

class EnvironmentFactory implements FactoryInterface
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
        $options  = $container->get('ZfcTwig\ModuleOptions');
        $envClass = $options->getEnvironmentClass();

        if (!$container->has($options->getEnvironmentLoader())) {
            throw new RuntimeException(
                sprintf(
                    'Loader with alias "%s" could not be found!',
                    $options->getEnvironmentLoader()
                )
            );
        }

        /** @var \Twig_Environment $env */
        $env = new $envClass($container->get($options->getEnvironmentLoader()), $options->getEnvironmentOptions());

        if ($options->getEnableFallbackFunctions()) {
            $helperPluginManager = $container->get('ViewHelperManager');
            $env->registerUndefinedFunctionCallback(
                function ($name) use ($helperPluginManager) {
                    if ($helperPluginManager->has($name)) {
                        return new FallbackFunction($name);
                    }
                    return false;
                }
            );
        }

        foreach ($options->getGlobals() as $name => $value) {
            $env->addGlobal($name, $value);
        }

        // Extensions are loaded later to avoid circular dependencies (for example, if an extension needs Renderer).
        return $env;
    }
}
