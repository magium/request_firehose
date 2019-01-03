<?php

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * To watch this in action execute `redis-cli` and call `blpop ingestion`.  Then run this file (`php push.php`).  Within
 * a fraction of a second you should see the message.  Use this method if you want to "persist" the data (ensure that you
 * don't miss any, or if you want to round-robin data processing.
 */
\Magium\RequestFirehose\RequestFirehose::getInstance()->setAdapter(
    new \Magium\RequestFirehose\Adapter\Redis\PushAdapter(
        new \Predis\Client('tcp://127.0.0.1:6379'),
        'ingestion'
    )
);
