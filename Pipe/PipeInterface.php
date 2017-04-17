<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;

interface PipeInterface
{
    /**
     * Read data from the pipe
     * @param boolean $blocking
     * @return string
     */
    public function read($blocking  = false);

    /**
     * Write data to the pipe
     * @param string $content
     * @param boolean $blocking
     * @return string
     */
    public function write($content, $blocking =  false);

    /**
     * Close the pipe
     * @return void
     */
    public function close();
}