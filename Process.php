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
     * signal handlers
     * @var array
     */
    protected $signalHandlers = [];

    /**
     * current status
     * @var string
     */
    protected $status;

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
    protected $ifSignaled;

    /**
     * The signal that caused the process to terminate
     * @var int
     */
    protected $termSignal;

    /**
     * The process If stopped
     * @var bool
     */
    protected $ifStopped;

    /**
     * The signal that caused the process to stop
     * @var int
     */
    protected $stopSignal;

    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException("Process expects a callable callback");
        }
        if (!function_exists('pcntl_fork')) {
            throw new RuntimeException("Process need ext-pcntl");
        }
        $this->callback = $callback;
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
            $this->status = static::STATUS_RUNNING;
        } else {
            $this->pid = posix_getpid();
            $this->installSignalHandlers();
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
        while (true) {
            if ($this->isRunning()) {
                usleep(1000);
            } else {
                break;
            }
        }
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
     * Gets the exit code of the process
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * Gets the error message for the exit code
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning()
    {
        //if process is not running, return false
        if ($this->status != static::STATUS_RUNNING) {
            return false;
        }
        //update child process status
        $this->updateStatus(false);
        return $this->status == static::STATUS_RUNNING;
    }

    /**
     * Sets the handler for a signal
     * @param int $signal
     * @param callable $handler
     */
    public function setSignalHandler($signal, $handler)
    {
        if (!is_callable($handler)) {
            throw new InvalidArgumentException('The signal handler should be callable');
        }
        $this->signalHandlers[$signal] = $handler;
    }

    /**
     * Gets the handler for a signal
     * @param $signal
     * @return int|string
     */
    public function getSignalHandler($signal)
    {
        if (isset($this->signalHandlers[$signal])) {
            return $this->signalHandlers[$signal];
        }
        return pcntl_signal_get_handler($signal);
    }

    /**
     * Installs all signal handlers
     * @return void
     */
    protected function installSignalHandlers()
    {
        foreach ($this->signalHandlers as $signal => $signalHandler) {
            pcntl_signal($signal, $signalHandler);
        }
        //The process stop its execution when the SIGTERM signal is received,
        pcntl_signal(SIGTERM, function(){
            exit(0);
        });
    }


    /**
     * Updates the status of the process
     * @param bool $blocking
     * @throws RuntimeException
     */
    protected function updateStatus($blocking = false)
    {
        if ($this->status != static::STATUS_RUNNING) {
            return;
        }
        $options = $blocking ? 0 : WNOHANG | WUNTRACED;
        $result = pcntl_waitpid($this->getPid(), $status, $options);
        if ($result == -1) {
            throw new RuntimeException("Error waits on or returns the status of the process");
        } elseif ($result) {
            //The process is terminated
            $this->status = static::STATUS_TERMINATED;
            //checks if the process is exited normally
            if (pcntl_wifexited($status)) {
                $this->exitCode = pcntl_wexitstatus($status);
                $this->errorMessage = pcntl_strerror($this->exitCode);
            }
            if (pcntl_wifsignaled($status)) {
                $this->ifSignaled = true;
                $this->termSignal = pcntl_wtermsig($status);
            }
            if (pcntl_wifstopped($status)) {
                $this->ifStopped = true;
                $this->stopSignal = pcntl_wifstopped($status);
            }
        } else {
            $this->status = static::STATUS_RUNNING;
        }
    }
}