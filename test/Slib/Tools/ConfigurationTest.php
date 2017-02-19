<?php namespace Kshabazz\Slib\Tests\Tools;

use Kshabazz\Slib\Tools\Configuration;

/**
 * Unit test for class Configuration
 *
 * @coversDefaultClass \Kshabazz\Slib\Tools\Configuration
 */
class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Kshabazz\Slib\Tools\Configuration|\PHPUnit\Framework\Mock\Object */
    private $configuration;

    public function setUp()
    {
        $this->configuration = $this->buildMockForTrait(Configuration::class);
    }
}
