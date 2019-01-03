<?php

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Run this file (`php filesystem.php`).  Run `tail -f /tmp/testfile` to watch the output.
 */

\Magium\RequestFirehose\RequestFirehose::getInstance()->setAdapter(
    new \Magium\RequestFirehose\Adapter\Filesystem\Filesystem('/tmp/testfile')
);
