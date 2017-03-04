<?php namespace Kshabazz\Slib\Tests\Mocks;

use Kshabazz\Slib\JsonModel;

class MakeT
{
    use JsonModel;

    private $company;

    public function __construct()
    {
        $this->company = '';
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($name)
    {
        $this->company = $name;
    }
}
