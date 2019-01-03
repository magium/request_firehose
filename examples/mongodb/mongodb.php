<?php

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * To watch this in action log in to your MongoDB instance and execute this file.  Once you have executed this file
 * check your mongo DB.  You should see the results there.
 */
$client = new \MongoDB\Client();
$collection = $client->selectCollection('analysis', 'requests');
\Magium\RequestFirehose\RequestFirehose::getInstance()->setAdapter(
    new \Magium\RequestFirehose\Adapter\MongoDb\MongoDb(
        $collection
    )
);
