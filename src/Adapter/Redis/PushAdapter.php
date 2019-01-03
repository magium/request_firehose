<?php

namespace Magium\RequestFirehose\Adapter\Redis;

class PushAdapter extends AbstractAdapter
{

    protected function send($data)
    {
        if ($this->endpoint) {
            $this->redis->lpush($this->endpoint, [$data]);
        }
    }

}
