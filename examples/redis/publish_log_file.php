<?php

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * To watch this in action execute `redis-cli` and call `subscribe ingestion`.  Then run this file
 * (`php -S 0.0.0.0:7777 publish_log_file.php`).  Within
 * a fraction of a second you should see the message.  Use this example if you do not want persistent data and/or
 * want multiple subscribers to watch the data, or if you want to tail the log file for your entire cluster you can
 * execute:
 *
 *  stdbuf -oL redis-cli --raw subscribe ingestion | grep -v -E 'message|ingestion'
 */
\Magium\RequestFirehose\RequestFirehose::getInstance()->setAdapter(
    new \Magium\RequestFirehose\Adapter\Redis\PublishAdapter(
        new \Predis\Client('tcp://127.0.0.1:6379'),
        'ingestion',
        0,
        new \Magium\RequestFirehose\Filter\LogfileFilter()
    )
);
