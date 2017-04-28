<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process;

class Status
{
    protected $status;

    protected $isExited = false;

    /**
     * exit code
     * @var int
     */
    protected $exitCode;

    /**
     * error message
     * @var string
     */
    protected $errorMessage;

    /**
     * If the signal that caused the process to terminate
     * @var boolean
     */
    protected $isSignaled = false;

    /**
     * The signal that caused the process to terminate
     * @var int
     */
    protected $terminateSignal;

    /**
     * The process If stopped
     * @var bool
     */
    protected $isStopped = false;

    /**
     * The signal that caused the process to stop
     * @var int
     */
    protected $stopSignal;

    public function __construct($status)
    {
        $this->status = $status;
        if ($this->isExited = pcntl_wifexited($status)) {
            $this->exitCode = pcntl_wexitstatus($status);
            $this->errorMessage = pcntl_strerror($this->exitCode);
        }
        if (pcntl_wifsignaled($status)) {
            $this->isSignaled = true;
            $this->terminateSignal = pcntl_wtermsig($status);
        }
        if (pcntl_wifstopped($status)) {
            $this->isStopped = true;
            $this->stopSignal = pcntl_wstopsig($status);
        }
    }

    /**
     * Checks if status code represents a normal exit.
     * @return bool
     */
    public function isExited()
    {
        return $this->isExited;
    }

    /**
     * Gets the exit code if the process is exited normally
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Check whether the process exits because of an signal
     * return boolean
     */
    public function isSignaled()
    {
        return $this->isSignaled;
    }

    /**
     * Gets the signal which caused the child to terminate.
     * @return int
     */
    public function getTerminateSignal()
    {
        return $this->terminateSignal;
    }

    /**
     * Checks whether the child process is currently stopped.
     * @return bool
     */
    public function isStopped()
    {
        return $this->isStopped;
    }

    /**
     * Gets the signal which caused the child to stop.
     * @return int
     */
    public function getStopSignal()
    {
        return $this->stopSignal;
    }

    /**
     * Checks whether the process is executed successfully
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->exitCode === 0;
    }
}
