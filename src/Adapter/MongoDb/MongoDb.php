<?php

namespace Magium\RequestFirehose\Adapter\MongoDb;

use Magium\RequestFirehose\Adapter\AdapterInterface;
use Magium\RequestFirehose\Filter\FilterInterface;
use MongoDB\Collection;

class MongoDb implements AdapterInterface
{

    protected $collection;
    protected $filter;

    public function __construct(Collection $collection, FilterInterface $filter = null)
    {
        $this->collection = $collection;
    }

    public function publish(array $data)
    {
        if ($this->filter instanceof FilterInterface) {
            $data = $this->filter->filter($data);
            if (!is_array($data)) {
                $data['request'] = $data;
            }
        }
        $this->collection->insertOne($data);
    }
}
