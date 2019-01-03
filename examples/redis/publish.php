<?php

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * To watch this in action execute `redis-cli` and call `subscribe ingestion`.  Then run this file (`php push.php`).  Within
 * a fraction of a second you should see the message.  Use this example if you do not want persistent data and/or
 * want multiple subscribers to watch the data.
 */
\Magium\RequestFirehose\RequestFirehose::getInstance()->setAdapter(
    new \Magium\RequestFirehose\Adapter\Redis\PublishAdapter(
        new \Predis\Client('tcp://127.0.0.1:6379'),
        'ingestion'
    )
);
