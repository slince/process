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
     * @return string
     */
    public function read();

    /**
     * Write data to the pipe
     * @param string $message
     * @return string
     */
    public function write($message);

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