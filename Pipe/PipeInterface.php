<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;

interface PipeInterface
{
    /**
     * Reads data from the pipe
     * @param boolean $blocking
     * @return string
     */
    public function read($blocking = null);

    /**
     * Write data to the pipe
     * @param string $message
     * @param boolean $blocking
     * @return string
     */
    public function write($message, $blocking = null);

    /**
     * Gets the stream of the pipe
     * @return resource
     */
    public function getStream();

    /**
     * Close the pipe
     * @return void
     */
    public function close();
}