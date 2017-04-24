<?php
include __DIR__ . '/vendor/autoload.php';

use Slince\Process\Pipe\ReadableFifo;
use Slince\Process\Pipe\WritableFifo;

$path = sys_get_temp_dir() . '/test.pipe';

$pid = pcntl_fork();

if ($pid > 0) {
    $fifo = new ReadableFifo($path, false);
//    echo $fifo->read();
    echo 'read ok', PHP_EOL;

    pcntl_wait($status);
} elseif ($pid == 0) {
    $fifo = new WritableFifo($path, false);
    $result = $fifo->write(str_repeat('hello', 12200000));
    var_dump($result);
    $fifo->setBlocking(true);
    $result = $fifo->write(str_repeat('world', 12200000));
    var_dump($result);
    echo 'write ok', PHP_EOL;
}