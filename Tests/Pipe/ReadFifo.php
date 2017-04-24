<?php
/**
 * Read some data form pipe
 * command like "php ./ReadFifo.php hello 2"
 */

function makeReadFifo()
{
    $pathname = '/tmp/test2.pipe';
    if (!file_exists($pathname)) {
        posix_mkfifo($pathname, 0666);
    }
    $fifo = fopen($pathname, 'r+');
    return $fifo;
}

$sleep = isset($argv[2]) ? $argv[2] : 0;
if ($sleep) {
    sleep($sleep);
}
$size = $argv[1];

$fifo = makeReadFifo();
$writedBytes = fread($fifo, $size);

exit;