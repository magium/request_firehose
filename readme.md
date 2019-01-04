# PHP Request Firehose

This library attaches itself to the PHP request and upon request shutdown it will send the request meta data or a log file entry to an adapter, currently Redis or Mongo, where it can be stored, processed or tailed.  It is intended for use in clustered systems where multiple machines are serving HTTP requests and, in general, where devops would like to have a centralized place for monitoring or storing requests that can be used alongside other logging tools.

For example, if you wanted to `tail` and `grep` the logs for a specific IP address you would deploy the Redis publish code (see below) and run the following command:

```
stdbuf -oL redis-cli --raw subscribe ingestion | grep -v -E 'message|ingestion' | grep '127.0.0.1'
```

# To install

```bash
composer install magium/request-firehose
```

# To Use
## Introduction

Call `\Magium\RequestFirehose\RequestFirehose::getInstance()` at the earliest convenience in your script.  This will typically be immediately after `require_once 'vendor/autoload.php';`.

Once you have an instance of the firehose you will need to configure an adapter for it.  The simples example is with the `Filesystem` adapter.

```php
\Magium\RequestFirehose\RequestFirehose::getInstance()->setAdapter(
    new \Magium\RequestFirehose\Adapter\Filesystem\Filesystem('/tmp/logfile')
);

```

That's it!  It will register itself as a shutdown function and write to the appropriate adapter after the request has completed.

If you are not using Composer, or are using some other autoloader that is not compatible with PSR-4 you can include the `src/include.php` file and it will load up all of the source files.

If you are using Redis, MongoDB, or some other adapter that is not part of core PHP you may need to install their client library.  Check the composer suggestions for supported clients.

Check out some of the examples in the [examples](examples) directory.  The following examples have been largely copied from there.

## Redis

### Push (BLPOP)

```php
/**
 * To watch this in action execute `redis-cli` and call `blpop ingestion`.  Then run this file (`php push.php`).  Use this method if you want to "persist" the data (ensure that you
 * don't miss any, or if you want to round-robin data processing.
 */
\Magium\RequestFirehose\RequestFirehose::getInstance()->setAdapter(
    new \Magium\RequestFirehose\Adapter\Redis\PushAdapter(
        new \Predis\Client('tcp://127.0.0.1:6379'),
        'ingestion'
    )
);
```

### PubSub (SUBSCRIBE)

```php
/**
 * To watch this in action execute `redis-cli` and call `subscribe ingestion`.  Then run this file (`php publish.php`).  Use this example if you do not want persistent data and/or
 * want multiple subscribers to watch the data.
 */
\Magium\RequestFirehose\RequestFirehose::getInstance()->setAdapter(
    new \Magium\RequestFirehose\Adapter\Redis\PublishAdapter(
        new \Predis\Client('tcp://127.0.0.1:6379'),
        'ingestion'
    )
);
```

### PubSub /w Log Output

If you are looking for something you can simply tail and pipe to grep, this might be a good option.

```php
/**
 * To watch this in action execute `redis-cli` and call `subscribe ingestion`.  Then run 
 * `php -S 0.0.0.0:7777 publish_log_file.php`).  Use this example if you do not want persistent data and/or
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
```

## MongoDB

```php
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
```

## Filesystem

```php
/**
 * Run this file (`php filesystem.php`).  Run `tail -f /tmp/testfile` to watch the output.
 */

\Magium\RequestFirehose\RequestFirehose::getInstance()->setAdapter(
    new \Magium\RequestFirehose\Adapter\Filesystem\Filesystem('/tmp/testfile')
);

```

## Aggregate

Perhaps there are times when you would like to push the data to Redis so you can tail the logs, but also store the data in Mongo for processing.  You can use the Aggregate adapter to do that.

```php

/**
 * To watch this in action execute `redis-cli` and call `subscribe ingestion`.  Then run
 * `php -S 0.0.0.0:7777 publish_log_file.php`.  Use this example if you want to watch your system by grepping
 * the Redis subscription output, but also want to store the requests in MongoDB for later analysis.
 *
 * stdbuf -oL redis-cli --raw subscribe ingestion | grep -v -E 'message|ingestion'
 *
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

```
