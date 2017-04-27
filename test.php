<?php
include __DIR__ . '/vendor/autoload.php';

use Slince\Process\Process;
use Slince\Process\Pipe\Pipe;

$message = 'The message from main process';


$path = __FILE__;


$process = new Process(function() use($path){
    $semaphore =  new \Slince\Process\SystemV\Semaphore($path);
    $semaphore->acquire();
    echo 'child', PHP_EOL;
    sleep(5);
    $semaphore->release();
    exit(0);
});
$process->start();

sleep(2);
$semaphore =  new \Slince\Process\SystemV\Semaphore($path);
$result = $semaphore->acquire(false);
var_dump($result);

$process->wait();
