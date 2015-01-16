<?php

namespace Mi24\Behat\SilexExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Mi24\Behat\SilexExtension\ServiceContainer\Driver\SilexFactory;
use Silex\Application;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage24.com>
 */
class SilexExtension implements Extension
{
    const APPLICATION_ID = 'silex_extension.application';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // get base path
        $basePath = $container->getParameter('paths.base');

        // find and require bootstrap
        $bootstrapPath = $container->getParameter(self::APPLICATION_ID.'.bootstrap');
        if (strlen($bootstrapPath) > 0) {
            if (file_exists($bootstrap = $basePath.DIRECTORY_SEPARATOR.$bootstrapPath) === true) {
                require_once $bootstrap;
            } elseif (file_exists($bootstrapPath) === true) {
                require_once $bootstrapPath;
            }
        }

        // find and require application
        $application     = null;
        $applicationPath = $container->getParameter(self::APPLICATION_ID.'.path');
        // find and require kernel
        if (file_exists($fullPath = $basePath.DIRECTORY_SEPARATOR.$applicationPath) === true) {
            $application     = require_once $fullPath;
            $applicationPath = $fullPath;
        } elseif (file_exists($applicationPath) === true) {
            $application = require_once $applicationPath;
        }

        /** @var Application $application */
        if ($application instanceof Application === false) {
            throw new \InvalidArgumentException(
                sprintf('Application loaded from "%s" is not an instance of "Silex/Application".', $applicationPath)
            );
        }

        $application['debug'] = $container->getParameter(self::APPLICATION_ID.'.debug');
        $container->set(self::APPLICATION_ID, $application);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'silex';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        if (null !== $minkExtension = $extensionManager->getExtension('mink')) {
            $minkExtension->registerDriverFactory(new SilexFactory());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('app')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('bootstrap')->defaultValue('app/bootstrap.php')->end()
                        ->scalarNode('path')->defaultValue('app/app.php')->end()
                        ->booleanNode('debug')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $container->setParameter(self::APPLICATION_ID.'.path', $config['app']['path']);
        $container->setParameter(self::APPLICATION_ID.'.debug', $config['app']['debug']);
        $container->setParameter(self::APPLICATION_ID.'.bootstrap', $config['app']['bootstrap']);
    }
}
