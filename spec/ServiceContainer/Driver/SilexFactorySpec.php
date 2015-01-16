<?php

namespace spec\Mi24\Behat\SilexExtension\ServiceContainer\Driver;

use Mi24\Behat\SilexExtension\ServiceContainer\SilexExtension;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage24.com>
 *
 * @mixin \Mi24\Behat\SilexExtension\ServiceContainer\Driver\SilexFactory
 */
class SilexFactorySpec extends ObjectBehavior
{
    public function it_is_a_driver_factory()
    {
        $this->shouldHaveType('Mi24\Behat\SilexExtension\ServiceContainer\Driver\SilexFactory');
    }

    public function it_is_named_silex()
    {
        $this->getDriverName()->shouldReturn('silex');
    }

    public function it_does_not_support_javascript()
    {
        $this->supportsJavascript()->shouldBe(false);
    }

    public function it_does_not_have_any_specific_configuration(ArrayNodeDefinition $builder)
    {
        $this->configure($builder);
    }

    public function it_creates_a_kernel_driver_definition()
    {
        $definition = $this->buildDriver(array());

        $definition->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Definition');
        $definition->getClass()->shouldBe('Mi24\Behat\SilexExtension\Driver\ApplicationDriver');
        $args = $definition->getArguments();
        $args[0]->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Reference');
        $args[0]->__toString()->shouldBe(SilexExtension::APPLICATION_ID);
    }
}
