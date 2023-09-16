<?php
include __DIR__ . '/../Utils.php';

use Slince\Process\Tests\Utils;

/**
 * Read some data form pipe
 * command like "php ./ReadFifo.php hello 2"
 */

$sleep = $argv[2] ?? 0;
if ($sleep) {
    sleep($sleep);
}
$size = $argv[1];
$fifo = Utils::makeNativeReadFifo('/tmp/test2.pipe');
fread($fifo, $size);
exit;
