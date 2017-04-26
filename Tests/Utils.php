<?php
namespace Slince\Process\Tests\Pipe;


final class Utils
{
    protected static $pd;

    /**
     * Creates a native read-only fifo fd
     * @param $pathname
     * @return bool|resource
     */
    public static function makeNativeReadFifo($pathname )
    {
        if (!file_exists($pathname)) {
            posix_mkfifo($pathname, 0666);
        }
        $fifo = fopen($pathname, 'r+');
        return $fifo;
    }

    /**
     * Creates a native write-only fifo fd
     * @param string $pathname
     * @return bool|resource
     */
    public static function makeNativeWriteFifo($pathname)
    {
        if (!file_exists($pathname)) {
            posix_mkfifo($pathname, 0666);
        }
        $fifo = fopen($pathname, 'r+');
        return $fifo;
    }

    /**
     * Executes the command
     * @param string $command
     * @return bool|resource
     */
    public static function asyncExecute($command)
    {
        $pd = popen($command, 'r');
        stream_set_blocking($pd, false);
        return $pd;
    }

    /**
     * Gets last pipe id
     * @return resource
     */
    public static function getLastPd()
    {
        return static::$pd;
    }
}