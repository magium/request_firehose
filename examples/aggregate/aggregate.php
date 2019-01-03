<?php

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * To watch this in action execute `redis-cli` and call `subscribe ingestion`.  Then run
 * `php -S 0.0.0.0:7777 publish_log_file.php`.  Use this example if you want to watch your system by grepping
 * the Redis subscription output, but also want to store the requests in MongoDB for later analysis.
 *
 * stdbuf -oL redis-cli --raw subscribe ingestion | grep -v -E 'message|ingestion'
 *
 * Redis output:
 * 2019-01-03T16:36:45-0600 127.0.0.1 GET /?asdga=2gr 200 "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36"
 * 2019-01-03T16:36:45-0600 127.0.0.1 GET /favicon.ico 200 "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36"
 *
 * MongoDB Document
 * {
"_id" : ObjectId("5c2e8e7dd420f41ab51450ad"),
"response_time" : 0.00007200241088867188,
"response_code" : 200,
"get" : {
"asdga" : "2gr"
},
"server" : {
"DOCUMENT_ROOT" : "/mnt/c/Projects/RequestFirehose/examples/aggregate",
"REMOTE_ADDR" : "127.0.0.1",
"REMOTE_PORT" : "34563",
"SERVER_SOFTWARE" : "PHP 7.3.0-2+ubuntu16.04.1+deb.sury.org+1 Development Server",
"SERVER_PROTOCOL" : "HTTP/1.1",
"SERVER_NAME" : "0.0.0.0",
"SERVER_PORT" : "7778",
"REQUEST_URI" : "/?asdga=2gr",
"REQUEST_METHOD" : "GET",
"SCRIPT_NAME" : "/",
"SCRIPT_FILENAME" : "aggregate.php",
"PHP_SELF" : "/",
"QUERY_STRING" : "asdga=2gr",
"HTTP_HOST" : "localhost:7778",
"HTTP_CONNECTION" : "keep-alive",
"HTTP_CACHE_CONTROL" : "max-age=0",
"HTTP_UPGRADE_INSECURE_REQUESTS" : "1",
"HTTP_USER_AGENT" : "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36",
"HTTP_ACCEPT" : "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*\/*;q=0.8",
        "HTTP_ACCEPT_ENCODING" : "gzip, deflate, br",
        "HTTP_ACCEPT_LANGUAGE" : "en-US,en;q=0.9",
        "REQUEST_TIME_FLOAT" : 1546555005.316124,
        "REQUEST_TIME" : 1546555005
    }
}
 */
$aggregate = new \Magium\RequestFirehose\Adapter\Aggregate\AggregateAdapter();
$aggregate[] = new \Magium\RequestFirehose\Adapter\Redis\PublishAdapter(
    new \Predis\Client('tcp://127.0.0.1:6379'),
    'ingestion',
    0,
    new \Magium\RequestFirehose\Filter\LogfileFilter()
);

$client = new \MongoDB\Client();
$collection = $client->selectCollection('analysis', 'requests');
$aggregate[] = new \Magium\RequestFirehose\Adapter\MongoDb\MongoDb(
    $collection
);

\Magium\RequestFirehose\RequestFirehose::getInstance()->setAdapter($aggregate);
