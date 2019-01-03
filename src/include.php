<?php

$dir = new RecursiveDirectoryIterator(__DIR__);
$files = new RecursiveIteratorIterator($dir);
/** @var $file SplFileInfo */
foreach ($files as $file) {
    if ($file->isFile() && $file->getRealPath() !== __FILE__) {
        require_once $file->getRealPath();
    }
}
