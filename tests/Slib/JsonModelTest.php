<?php namespace Kshabazz\Slib\Tests;

use Kshabazz\Slib\JsonModel;
use Kshabazz\Slib\JtpException;
use Kshabazz\Slib\Tests\Mocks\ModelT;

/**
 * Class JsonModelTest
 *
 * @package \Kshabazz\Slib\Tests\Models
 * @coversDefaultClass \Kshabazz\Slib\JsonModel
 */
class JsonModelTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Kshabazz\Slib\JsonModel|\PHPUnit\Framework\MockObject\MockObject */
    private $model;

    public function setUp()
    {
        $this->model = $this->getMockForTrait(JsonModel::class);
    }

    /**
     * @covers ::validateProperty
     */
    public function testCanValidateProperty()
    {
        $fixture = 1234;
        $actual = $this->model->validateProperty('test', 'integer', $fixture);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::validateProperty
     */
    public function testCanInvalidateProperty()
    {
        $this->expectException(JtpException::class);
        $this->expectExceptionCode(JtpException::PROPERTY_EMPTY);

        $this->model->validateProperty('test', 'NULL', null);
    }

    /**
     * @covers ::validateProperty
     */
    public function testCanInvalidatePropertyType()
    {
        $this->expectException(JtpException::class);
        $this->expectExceptionCode(JtpException::BAD_PROPERTY_TYPE);

        $this->model->validateProperty('test', 'integer', '1');
    }

    /**
     * @covers ::setByJson
     * @uses \Kshabazz\Slib\JsonModel::__toString
     * @uses \Kshabazz\Slib\JsonModel::jsonSerialize
     */
    public function testCanSetByJsonString()
    {
        $fixture = '{"name":1234, "year": null}';
        $mt = new ModelT();
        $mt->setByJson($fixture);

        $this->assertEquals(1234, $mt->getName());
    }

    /**
     * @covers ::__toString
     * @covers ::jsonSerialize
     */
    public function testCanCastModelToSString()
    {
        $mt = new ModelT();
        $mt->setName('test');
        $actual = (string) $mt;

        $this->assertEquals('{"name":"test"}', $actual);
    }

    /**
     * @covers ::__toString
     * @covers ::jsonSerialize
     */
    public function testCastingToAStringWillOmitPropertiesSetToNull()
    {
        $mt = new ModelT();
        $mt->setName(null);
        $actual = (string) $mt;

        $this->assertEquals('{}', $actual);
    }

    /**
     * @covers ::setByObject
     */
    public function testCanInitializeAModelWithStdClassObject()
    {
        $fixture = new \stdClass();
        $fixture->name = 'test';
        $mt = new ModelT();
        $mt->setByObject($fixture);

        $this->assertEquals('test', $mt->getName());
    }

    /**
     * @covers ::rSetByObject
     * @covers ::hydrateClass
     */
    public function testCanInitializeAModelWithStdClassObjectRecursively()
    {
        $fixture = new \stdClass();
        $fixture->name = 'test';
        $fixture->makeT = new \stdClass();
        $fixture->makeT->company = 'Kohirens';

        $mt = new ModelT();
        $mt->rSetByObject($fixture, '\\Kshabazz\Slib\\Tests\\Mocks');
        $actual = $mt->getMakeT();

        $this->assertEquals('Kohirens', $actual->getCompany());
    }

    /**
     * @covers ::rSetByObject
     * @covers ::hydrateClass
     */
    public function testCanInitializeAModelWithAnArrayOfStdClassObjectsRecursively()
    {
        $fixture = new \stdClass();
        $fixture->engine = [];
        $fixture->engine[] = new \stdClass();
        $fixture->engine[0]->serial = 1234;

        $mt = new ModelT();
        $mt->rSetByObject($fixture, '\\Kshabazz\Slib\\Tests\\Mocks');
        $actual = $mt->getEngine();

        $this->assertEquals(1234, $actual[0]->getSerial());
    }
}
