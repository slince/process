<?php
include __DIR__ . '/FifoUtils.php';

use Slince\Process\Tests\Pipe\FifoUtils;

/**
 * Read some data form pipe
 * command like "php ./ReadFifo.php hello 2"
 */

$sleep = isset($argv[2]) ? $argv[2] : 0;
if ($sleep) {
    sleep($sleep);
}
$size = $argv[1];
$fifo = FifoUtils::makeNativeReadFifo('/tmp/test2.pipe');
fread($fifo, $size);
exit;