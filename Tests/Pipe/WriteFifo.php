<?php
include __DIR__ . '/../Utils.php';

use Slince\Process\Tests\Utils;

/**
 * Writes some data to pipe
 * command like "php ./WriteFifo hello 2"
 */

$fifo = Utils::makeNativeWriteFifo('/tmp/test1.pipe');

$message = $argv[1];
fwrite($fifo, $message);

$sleep = isset($argv[2]) ? $argv[2] : 0;
if ($sleep) {
    sleep($sleep);
}
exit;