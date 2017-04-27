<?php
include __DIR__ . '/vendor/autoload.php';

use Slince\Process\Process;

$process = new Process(function(){
    sleep(1);
    throw new RuntimeException('hahaha');
});



$process->getSignalHandler()->register(SIGTERM, function(){
    echo "hello, i'm " . getmypid(), PHP_EOL;
});

$process->getSignalHandler()->register(SIGTERM, SIG_IGN);

$process->start();

$process->signal(SIGTERM);

$process->wait();

var_dump($process->getStatus()->isStopped());