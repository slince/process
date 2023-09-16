<?php
namespace Slince\Process\Tests;

final class Utils
{
    protected static $pd;

    /**
     * Creates a native read-only fifo fd
     * @param $pathname
     * @return bool|resource
     */
    public static function makeNativeReadFifo($pathname)
    {
        if (!file_exists($pathname)) {
            posix_mkfifo($pathname, 0666);
        }
        return fopen($pathname, 'r+');
    }

    /**
     * Creates a native write-only fifo fd
     * @param string $pathname
     * @return bool|resource
     */
    public static function makeNativeWriteFifo(string $pathname)
    {
        if (!file_exists($pathname)) {
            posix_mkfifo($pathname, 0666);
        }
        return fopen($pathname, 'r+');
    }

    /**
     * Executes the command
     * @param string $command
     * @return bool|resource
     */
    public static function asyncExecute(string $command)
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
        return Utils::$pd;
    }
}
