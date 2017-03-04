<?php namespace Kshabazz\Slib\Tests\Mocks;

use Kshabazz\Slib\JsonModel;

class Engine
{
    use JsonModel;

    private $serial;

    public function getSerial()
    {
        return $this->serial;
    }

    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }
}
