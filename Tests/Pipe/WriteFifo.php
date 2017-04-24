<?php
/**
 * Writes some data to pipe
 * command like "php ./WriteFifo hello 2"
 */

function makeWriteFifo()
{
    $pathname = '/tmp/test1.pipe';
    if (!file_exists($pathname)) {
        posix_mkfifo($pathname, 0666);
    }
    $fifo = fopen($pathname, 'w+');
    return $fifo;
}

$fifo = makeWriteFifo();
$message = $argv[1];
$writedBytes = fwrite($fifo, $message);

$sleep = isset($argv[2]) ? $argv[2] : 0;
if ($sleep) {
    sleep($sleep);
}
exit;