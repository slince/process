<?php
include __DIR__ . '/vendor/autoload.php';

use Slince\Process\Process;
use Slince\Process\Pipe\Pipe;

$message = 'The message from main process';


$pid = pcntl_fork();
$path = sys_get_temp_dir() . '/test.pipe';
if (!file_exists($path)) {
    posix_mkfifo($path, 0666);
}


if ($pid > 0) {
    sleep(2);
    echo 'start open', PHP_EOL;
    $fifo = fopen($path, 'r');
    echo 'start read', PHP_EOL;

    $read = [$fifo];
    $write = [];
    $except  = [];
    $timeout = null;

//    stream_set_blocking($fifo, false);
//    $message = fread($fifo, 1024);

//    $message = stream_get_contents($fifo);
//    if (stream_select($read, $write, $except, $timeout) > 0) {
////        $message = fread($fifo, 1024);
//        $message = stream_get_contents($fifo);
//    }
//
    var_dump(__LINE__, $message);
    echo 'read ok', PHP_EOL;
    pcntl_wait($status);
} elseif($pid == 0) {

    $fifo = fopen($path, 'w+');
    stream_set_blocking($fifo, true);
    $result = fwrite($fifo, str_repeat('hello', 102000));
    var_dump($result);
    echo 'send ok', PHP_EOL;
    sleep(5);
    exit();
} else {
    exit('fork error');
}