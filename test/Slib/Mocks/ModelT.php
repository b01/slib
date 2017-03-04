<?php namespace Kshabazz\Slib\Tests\Mocks;

use Kshabazz\Slib\JsonModel;

/**
 * Class ModelT
 *
 * @package \Kshabazz\Slib\Tests\Mocks
 */
class ModelT
{
    use JsonModel;

    /**
     *
     * @var array
     */
    private $engine;

    /**
     *
     * @var \Kshabazz\Slib\Tests\Mocks\MakeT;
     */
    private $makeT;

    /**
     * @var string
     */
    private $name;

    /**
     * @return array
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     *
     * @return \Kshabazz\Slib\Tests\Mocks\MakeT
     */
    public function getMakeT()
    {
        return $this->makeT;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \Kshabazz\Slib\Tests\Mocks\Engine $engine
     * @return $this
     */
    public function setEngine(Engine $engine)
    {
        if (!isset($this->engine)) {
            $this->engine = [];
        }

        $this->engine[] = $engine;

        return $this;
    }

    /**
     * @param \Kshabazz\Slib\Tests\Mocks\MakeT $makeT
     */
    public function setMakeT(MakeT $makeT)
    {
        $this->makeT = $makeT;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
