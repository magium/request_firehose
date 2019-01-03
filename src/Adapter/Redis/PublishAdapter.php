<?php

namespace Magium\RequestFirehose\Adapter\Redis;

class PublishAdapter extends AbstractAdapter
{

    protected function send($data)
    {
        if ($this->endpoint) {
            $this->redis->publish($this->endpoint, $data);
        }
    }

}
