<?php
namespace Slince\Process\Tests\Pipe;

/**
 * Writes some data to pipe
 * command like "php ./WriteFifo hello 2"
 */

$fifo = FifoUtils::makeNativeWriteFifo('/tmp/test1.pipe');
$message = $argv[1];
$writedBytes = fwrite($fifo, $message);

$sleep = isset($argv[2]) ? $argv[2] : 0;
if ($sleep) {
    sleep($sleep);
}
exit;