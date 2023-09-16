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

interface ProcessInterface
{
    /**
     * process status,running
     * @var string
     */
    const STATUS_READY = 'ready';

    /**
     * process status,running
     * @var string
     */
    const STATUS_RUNNING = 'running';

    /**
     * process status,stopped
     * @var string
     */
    const STATUS_STOPPED = 'stopped';

    /**
     * process status,terminated
     * @var string
     */
    const STATUS_TERMINATED = 'terminated';

    /**
     * process status,exited
     * @var string
     */
    const STATUS_EXITED = 'exited';

    /**
     * Starts the process.
     */
    public function start(): void;

    /**
     * Wait for the process exit.
     */
    public function wait();

    /**
     * Starts and wait the process.
     * @return void
     */
    public function run(): void;

    /**
     * Stop the process.
     */
    public function stop(): void;

    /**
     * Resume the process.
     */
    public function continue(): void;

    /**
     * Terminate the process with an optional signal.
     * @param int $signal
     */
    public function terminate(int $signal = SIGKILL);

    /**
     * Sends a POSIX signal to the process.
     *
     * @param int $signal A valid POSIX signal (see https://php.net/pcntl.constants)
     *
     * @throws LogicException   In case the process is not running
     * @throws RuntimeException In case --enable-sigchild is activated and the process can't be killed
     * @throws RuntimeException In case of failure
     */
    public function signal(int $signal);

    /**
     * Gets the process id.
     *
     * @return int|null
     */
    public function getPid(): ?int;

    /**
     * Checks whether the process is running.
     *
     * @return bool
     */
    public function isRunning(): bool;

    /**
     * Checks if the process is stopped.
     *
     * @return bool true if process is stopped, false otherwise
     */
    public function isStopped(): bool;

    /**
     * Checks if the process is terminated.
     *
     * @return bool true if process is terminated, false otherwise
     */
    public function isTerminated(): bool;

    /**
     * Checks if the process is exited.
     *
     * @return bool true if process is exited, false otherwise
     */
    public function isExited(): bool;

    /**
     * Gets the process status.
     *
     * The status is one of: ready, started, terminated.
     *
     * @return string The current process status
     */
    public function getStatus(): string;

    /**
     * Returns the exit code returned by the process.
     *
     * @return int|null The exit status code
     */
    public function getExitCode(): ?int;

    /**
     * Returns the exit code text returned by the process.
     *
     * @return string|null The exit status code text
     */
    public function getExitCodeText(): ?string;

    /**
     * Returns the number of the signal that caused the child process to terminate its execution.
     *
     * It is only meaningful if hasBeenSignaled() returns true.
     *
     * @return int|null
     *
     * @throws RuntimeException In case --enable-sigchild is activated
     * @throws LogicException   In case the process is not terminated
     */
    public function getTermSignal(): ?int;

    /**
     * Returns the number of the signal that caused the child process to stop its execution.
     *
     * It is only meaningful if hasBeenStopped() returns true.
     *
     * @return int|null
     *
     * @throws LogicException In case the process is not terminated
     */
    public function getStopSignal(): ?int;

    /**
     * Returns true if the child process has been continued.
     *
     * It always returns false on Windows.
     *
     * @return bool
     */
    public function hasBeenContinued(): bool;
}
