<?php

declare(strict_types=1);

/*
 * This file is part of the slince/process package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Slince\Process;

use Slince\Process\Exception\LogicException;
use Slince\Process\Exception\RuntimeException;

final class Process implements ProcessInterface
{
    /**
     * @var string
     */
    protected string $status = self::STATUS_READY;

    /**
     * @var \Closure
     */
    protected \Closure $callback;

    protected ?int $pid = null;

    protected ?int $statusInfo = null;

    protected static CurrentProcess $currentProcess;

    public function __construct(callable $callback)
    {
        if (!function_exists('pcntl_fork')) {
            throw new RuntimeException('The Process class relies on ext-pcntl, which is not available on your PHP installation.');
        }
        if ($callback instanceof \Closure) {
            \Closure::bind($callback, null);
        }
        $this->callback = $callback;
    }

    /**
     * Returns the current process instance.
     * @return CurrentProcess
     */
    public static function current(): CurrentProcess
    {
        if (null === self::$currentProcess) {
            self::$currentProcess = new CurrentProcess();
        }
        return self::$currentProcess;
    }

    /**
     * Checks whether support signal.
     * @return bool
     */
    public static function isSupportPosixSignal(): bool
    {
        return function_exists('pcntl_signal');
    }

    /**
     * {@inheritdoc}
     */
    public function start(): void
    {
        if ($this->isRunning()) {
            throw new RuntimeException("The process is already running");
        }
        $pid = \pcntl_fork();
        if ($pid == -1) {
            throw new RuntimeException("Could not fork");
        } elseif ($pid) { //Records the pid of the child process
            $this->pid = $pid;
            $this->status = self::STATUS_RUNNING;
            $this->updateStatus(false);
        } else {
            try {
                $exitCode = call_user_func($this->callback);
            } catch (\Exception $e) {
                $exitCode  = 255;
            }
            exit(intval($exitCode));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function wait(): void
    {
        $this->requireProcessIsStarted(__FUNCTION__);
        $this->updateStatus(true);
    }

    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        $this->start();
        $this->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function getPid(): ?int
    {
        return $this->pid;
    }

    /**
     * {@inheritdoc}
     */
    public function signal(int $signal): void
    {
        if (!$this->isRunning()) {
//            throw new RuntimeException("The process is not currently running");
        }
        posix_kill($this->getPid(), $signal);
    }

    protected function updateStatus(bool $blocking): void
    {
        if (self::STATUS_RUNNING !== $this->status) {
            return;
        }
        $options = $blocking ? 0 : WNOHANG | WUNTRACED;
        $result = pcntl_waitpid($this->getPid(), $this->statusInfo, $options);
        if ($result == -1) {
            throw new RuntimeException("Error waits on or returns the status of the process");
        } elseif ($result === 0) {
            $this->status = self::STATUS_RUNNING;
        } else {
            //The process is terminated
            $this->status = self::STATUS_TERMINATED;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function stop(): void
    {
        $this->signal(SIGSTOP);
    }

    /**
     * {@inheritdoc}
     */
    public function continue(): void
    {
        $this->signal(SIGCONT);
    }


    /**
     * {@inheritdoc}
     */
    public function terminate(int $signal = SIGTERM): void
    {
        $this->signal($signal);
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted(): bool
    {
        return self::STATUS_RUNNING === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning(): bool
    {
        //if process is not running, return false
        if (self::STATUS_RUNNING !== $this->status) {
            return false;
        }
        //if the process is running, update process status again
        $this->updateStatus(false);
        return self::STATUS_RUNNING === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function isTerminated(): bool
    {
        $this->updateStatus(false);

        return self::STATUS_TERMINATED === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): string
    {
        $this->updateStatus(false);

        return $this->status;
    }

    /**
     * Ensures the process is running or terminated, throws a LogicException if the process has a not started.
     *
     * @throws LogicException if the process has not run
     */
    private function requireProcessIsStarted(string $functionName): void
    {
        if (!$this->isStarted()) {
            throw new LogicException(sprintf('Process must be started before calling "%s()".', $functionName));
        }
    }

    /**
     * Ensures the process is terminated, throws a LogicException if the process has a status different than "terminated".
     *
     * @throws LogicException if the process is not yet terminated
     */
    private function requireProcessIsTerminated(string $functionName): void
    {
        if (!$this->isTerminated()) {
            throw new LogicException(sprintf('Process must be terminated before calling "%s()".', $functionName));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasBeenExited(): bool
    {
        $this->requireProcessIsTerminated(__FUNCTION__);
        return pcntl_wifexited($this->statusInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function getExitCode(): int
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return pcntl_wexitstatus($this->statusInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBeenSignaled(): bool
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return pcntl_wifsignaled($this->statusInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function getTermSignal(): int
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return pcntl_wtermsig($this->statusInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBeenStopped(): bool
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return pcntl_wifstopped($this->statusInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function getStopSignal(): int
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return pcntl_wstopsig($this->statusInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBeenContinued(): bool
    {
        $this->requireProcessIsStarted(__FUNCTION__);
        return pcntl_wifcontinued($this->statusInfo);
    }
}
