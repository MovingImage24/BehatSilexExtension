<?php

namespace Mi24\Behat\SilexExtension\ServiceContainer\Driver;

use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use Mi24\Behat\SilexExtension\ServiceContainer\SilexExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage24.com>
 */
class SilexFactory implements DriverFactory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'silex';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsJavascript()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        if (!class_exists('Behat\Mink\Driver\BrowserKitDriver')) {
            throw new \RuntimeException(
                'Install MinkBrowserKitDriver in order to use the silex driver.'
            );
        }

        return new Definition('Mi24\Behat\SilexExtension\Driver\ApplicationDriver', array(
            new Reference(SilexExtension::APPLICATION_ID),
            '%mink.base_url%',
        ));
    }
}
