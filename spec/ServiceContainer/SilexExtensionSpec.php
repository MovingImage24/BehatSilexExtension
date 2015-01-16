<?php

namespace spec\Mi24\Behat\SilexExtension\ServiceContainer;

use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage24.com>
 *
 * @mixin \Mi24\Behat\SilexExtension\ServiceContainer\SilexExtension
 */
class SilexExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Mi24\Behat\SilexExtension\ServiceContainer\SilexExtension');
    }

    public function it_is_named_silex()
    {
        $this->getConfigKey()->shouldReturn('silex');
    }

    public function it_should_initialize_silex_driver_factory_for_mink(MinkExtension $minkExtension)
    {
        $minkExtension->getConfigKey()->willReturn('mink');

        $minkExtension->registerDriverFactory(
            Argument::type('Mi24\Behat\SilexExtension\ServiceContainer\Driver\SilexFactory')
        )->shouldBeCalled();
        $extensionManager = new ExtensionManager([$minkExtension->getWrappedObject()]);
        $this->initialize($extensionManager);
    }

    public function it_should_load_the_silex_parameters(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->setParameter('silex_extension.application.path', 'path')->shouldBeCalled();
        $containerBuilder->setParameter('silex_extension.application.debug', false)->shouldBeCalled();
        $containerBuilder->setParameter('silex_extension.application.bootstrap', 'bootstrap_path')->shouldBeCalled();

        $this->load(
            $containerBuilder,
            array('app' => array('path' => 'path', 'debug' => false, 'bootstrap' => 'bootstrap_path'))
        );
    }

    public function it_should_initialize_the_silex_application_from_base_path(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->getParameter('silex_extension.application.path')->willReturn('fixtures/app.php');
        $containerBuilder->getParameter('silex_extension.application.bootstrap')->willReturn('fixtures/bootstrap.php');
        $containerBuilder->getParameter('silex_extension.application.debug')->willReturn(true);
        $containerBuilder->getParameter('paths.base')->willReturn(__DIR__.'/..');

        $containerBuilder->set('silex_extension.application', Argument::type('Silex\Application'))->shouldBeCalled();

        $this->process($containerBuilder);
    }

    public function it_should_initialize_the_silex_application_from_full_application_path(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->getParameter('silex_extension.application.path')
            ->willReturn(__DIR__.'/../fixtures/app_direct.php');
        $containerBuilder->getParameter('silex_extension.application.bootstrap')
            ->willReturn(__DIR__.'/../fixtures/bootstrap_direct.php');
        $containerBuilder->getParameter('silex_extension.application.debug')->willReturn(true);
        $containerBuilder->getParameter('paths.base')->willReturn(__DIR__);

        $containerBuilder->set('silex_extension.application', Argument::type('Silex\Application'))->shouldBeCalled();

        $this->process($containerBuilder);
    }

    public function it_should_fail_if_no_application_is_set(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->getParameter('silex_extension.application.path')->willReturn('app.php');
        $containerBuilder->getParameter('silex_extension.application.bootstrap')->willReturn(null);
        $containerBuilder->getParameter('silex_extension.application.debug')->willReturn(true);
        $containerBuilder->getParameter('paths.base')->willReturn(__DIR__);

        $this->shouldThrow('\InvalidArgumentException')->duringProcess($containerBuilder);
    }
}
