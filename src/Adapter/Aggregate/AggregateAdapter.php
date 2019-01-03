<?php

namespace Magium\RequestFirehose\Adapter\Aggregate;

use Magium\RequestFirehose\Adapter\AdapterInterface;

class AggregateAdapter extends \ArrayObject implements AdapterInterface
{

    public function publish(array $data)
    {
        foreach ($this as $adapter) {
            if ($adapter instanceof AdapterInterface) {
                $adapter->publish($data);
            }
        }
    }

}
