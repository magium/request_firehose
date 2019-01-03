<?php

namespace Magium\RequestFirehose;

use Magium\RequestFirehose\Adapter\AdapterInterface;

class RequestFirehose
{

    protected static $me;
    protected $startTime;
    protected $adapter;
    protected $get;
    protected $server;

    private function __construct()
    {
        $this->startTime = microtime(true);
        $this->get = $_GET;
        $this->server = $_SERVER;
        register_shutdown_function([$this, 'shutdown']);
    }

    /**
     * @return RequestFirehose
     */
    public static function getInstance()
    {
        if (!self::$me instanceof RequestFirehose) {
            self::$me = new RequestFirehose();
        }
        return self::$me;
    }

    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function shutdown()
    {
        $meta = [
            'response_time' => (microtime(true) - $this->startTime),
            'response_code' => http_response_code(),
            'get' => $this->get,
            'server' => $this->server
        ];
        if ($this->adapter instanceof AdapterInterface) {
            $this->adapter->publish($meta);
        }

    }

}
