<?php

namespace Magium\RequestFirehose\Tests;

use Magium\RequestFirehose\Adapter\AbstractAdapter;
use Magium\RequestFirehose\Adapter\AdapterInterface;
use Magium\RequestFirehose\Config;
use Magium\RequestFirehose\RequestFirehose;
use PHPUnit\Framework\TestCase;

class RequestFirehoseTest extends TestCase
{

    protected function getMockInstance(AdapterInterface $adapter): RequestFirehose
    {
        $builder = $this->getMockBuilder(RequestFirehose::class)->disableOriginalConstructor();
        $builder->setMethods(null);
        $mock = $builder->getMock();
        $mock->setAdapter($adapter);
        return $mock;
    }

    public function testPublish()
    {
        $adapter = $this->getMockBuilder(AdapterInterface::class);
        $adapter->setMethods(['publish']);
        $mock = $adapter->getMock();
        $mock->expects(self::once())->method('publish');
        $instance = $this->getMockInstance($mock);
        $instance->shutdown();
    }

}
