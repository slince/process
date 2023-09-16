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
     * Checks if the process has been started with no regard to the current state.
     *
     * @return bool true if status is ready, false otherwise
     */
    public function isStarted(): bool;

    /**
     * Checks if the process is terminated.
     *
     * @return bool true if process is terminated, false otherwise
     */
    public function isTerminated(): bool;

    /**
     * Gets the process status.
     *
     * The status is one of: ready, started, terminated.
     *
     * @return string The current process status
     */
    public function getStatus(): string;

    /**
     * Returns true if the child process has been exits.
     *
     * It always returns false on Windows.
     *
     * @return bool
     *
     * @throws LogicException In case the process is not terminated
     */
    public function hasBeenExited(): bool;

    /**
     * Returns the exit code returned by the process.
     *
     * @return int The exit status code
     */
    public function getExitCode(): int;

    /**
     * Returns true if the child process has been terminated by an uncaught signal.
     *
     * It always returns false on Windows.
     *
     * @return bool
     *
     * @throws LogicException In case the process is not terminated
     */
    public function hasBeenSignaled(): bool;

    /**
     * Returns the number of the signal that caused the child process to terminate its execution.
     *
     * It is only meaningful if hasBeenSignaled() returns true.
     *
     * @return int
     *
     * @throws RuntimeException In case --enable-sigchild is activated
     * @throws LogicException   In case the process is not terminated
     */
    public function getTermSignal(): int;

    /**
     * Returns true if the child process has been stopped by a signal.
     *
     * It always returns false on Windows.
     *
     * @return bool
     *
     * @throws LogicException In case the process is not terminated
     */
    public function hasBeenStopped(): bool;

    /**
     * Returns the number of the signal that caused the child process to stop its execution.
     *
     * It is only meaningful if hasBeenStopped() returns true.
     *
     * @return int
     *
     * @throws LogicException In case the process is not terminated
     */
    public function getStopSignal(): int;

    /**
     * Returns true if the child process has been continued.
     *
     * It always returns false on Windows.
     *
     * @return bool
     */
    public function hasBeenContinued(): bool;
}
