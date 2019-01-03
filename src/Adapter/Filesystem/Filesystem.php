<?php

namespace Magium\RequestFirehose\Adapter\Filesystem;

use Magium\RequestFirehose\Adapter\AdapterInterface;
use Magium\RequestFirehose\Filter\FilterInterface;
use Magium\RequestFirehose\Filter\LogfileFilter;

class Filesystem implements AdapterInterface
{

    protected $logFile;
    protected $filter;

    public function __construct($logFile, FilterInterface $filter = null)
    {
        $this->logFile = $logFile;
        if (!$filter instanceof FilterInterface) {
            $filter = new LogfileFilter();
        }
        $this->filter = $filter;
    }

    public function publish(array $data)
    {
        if ($this->filter instanceof FilterInterface) {
            $data = $this->filter->filter($data);
        }
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $fh = fopen($this->logFile, 'a');
        if ($fh) {
            flock($fh, LOCK_EX);
            fwrite($fh, $data . "\n");
            fclose($fh);
        }

    }

}
