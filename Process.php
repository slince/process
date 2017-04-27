<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process;

use Slince\Process\Exception\InvalidArgumentException;
use Slince\Process\Exception\RuntimeException;

class Process implements ProcessInterface
{
    /**
     * process status,running
     * @var string
     */
    const STATUS_RUNNING = 'running';

    /**
     * process status,terminated
     * @var string
     */
    const STATUS_TERMINATED = 'terminated';

    /**
     * callback
     * @var callable
     */
    protected $callback;

    /**
     * pid
     * @var int
     */
    protected $pid;

    /**
     * Whether the process is running
     * @var bool
     */
    protected $isRunning = false;

    /**
     * signal handler
     * @var SignalHandler
     */
    protected $signalHandler;

    /**
     * current status
     * @var Status
     */
    protected $status;

    public function __construct($callback)
    {
        if (!static::isSupported()) {
            throw new RuntimeException("Process need ext-pcntl");
        }
        if (!is_callable($callback)) {
            throw new InvalidArgumentException("Process expects a callable callback");
        }
        $this->callback = $callback;
        $this->signalHandler = SignalHandler::create();
    }

    /**
     * Checks whether the current environment supports this
     * @return bool
     */
    public static function isSupported()
    {
        return function_exists('pcntl_fork');
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->isRunning()) {
            throw new RuntimeException("The process is already running");
        }
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new RuntimeException("Could not fork");
        } elseif ($pid) { //Records the pid of the child process
            $this->pid = $pid;
            $this->isRunning = true;
        } else {
            $this->pid = posix_getpid();
            try {
                $exitCode = call_user_func($this->callback);
            } catch (\Exception $e) {
                $exitCode  = 1;
            }
            exit(intval($exitCode));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function wait()
    {
        if ($this->isRunning()) {
            $this->updateStatus(true);
        }
    }

    /**
     * Start and wait for the process to complete
     */
    public function run()
    {
        $this->start();
        $this->wait();
    }

    /**
     * Stops the process
     * @param int $signal
     * @return int
     */
    public function stop($signal = SIGTERM)
    {
        $this->signal($signal);
        $this->updateStatus();
        return $this->getExitCode();
    }


    /**
     * {@inheritdoc}
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * {@inheritdoc}
     */
    public function signal($signal)
    {
        if (!$this->isRunning()) {
            throw new RuntimeException("The process is not currently running");
        }
        posix_kill($this->getPid(), $signal);
    }

    /**
     * @return SignalHandler
     */
    public function getSignalHandler()
    {
        return $this->signalHandler;
    }

    /**
     * Gets the exit code of the process
     * @return int
     */
    public function getExitCode()
    {
        return $this->status ? $this->status->getExitCode() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning()
    {
        //if process is not running, return false
        if (!$this->isRunning) {
            return false;
        }
        //update child process status
        $this->updateStatus(false);
        return $this->isRunning;
    }

    /**
     * Updates the status of the process
     * @param bool $blocking
     * @throws RuntimeException
     */
    protected function updateStatus($blocking = false)
    {
        if (!$this->isRunning) {
            return;
        }
        $options = $blocking ? 0 : WNOHANG | WUNTRACED;
        $result = pcntl_waitpid($this->getPid(), $status, $options);
        if ($result == -1) {
            throw new RuntimeException("Error waits on or returns the status of the process");
        } elseif ($result) {
            //The process is terminated
            $this->isRunning = false;
            //checks if the process is exited normally
            $this->status = new Status($status);
        } else {
            $this->isRunning = true;
        }
    }

    /**
     * Gets the status of the process
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }
}