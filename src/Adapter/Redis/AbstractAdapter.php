<?php

namespace Magium\RequestFirehose\Adapter\Redis;

use Magium\RequestFirehose\Adapter\AdapterInterface;
use Magium\RequestFirehose\Filter\FilterInterface;
use Predis\Client;

abstract class AbstractAdapter implements AdapterInterface
{
    protected $redis;
    protected $endpoint;
    protected $database;
    protected $filter;

    public function __construct(Client $redis, $endpoint, $database = 0, FilterInterface $filter = null)
    {
        $this->redis = $redis;
        $this->endpoint = $endpoint;
        $this->database;
        $this->filter = $filter;
    }

    public function publish(array $data)
    {
        if ($this->endpoint) {
            $this->redis->select($this->database);
            if ($this->filter instanceof FilterInterface) {
                $data = $this->filter->filter($data);
            } else {
                $data = json_encode($data);
            }
            $this->send($data);
        }
    }

    abstract protected function send($data);

}
