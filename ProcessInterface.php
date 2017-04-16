<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process;

interface ProcessInterface
{
    /**
     * Returns the Pid (process identifier)
     * @return resource
     */
    public function getPid();

    /**
     * Starts the process
     */
    public function start();

    /**
     * Waits for the process to terminate.
     */
    public function wait();

    /**
     * Sends a posix signal to the process.
     * @param int $signal pcntl sinal
     * @return boolean
     */
    public function signal($signal);

    /**
     * Checks if the process is currently running
     * @return bool
     */
    public function isRunning();
}