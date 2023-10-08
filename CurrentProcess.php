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

final class CurrentProcess
{
    /**
     * @var int|null
     */
    protected ?int $pid;

    public function __construct()
    {
        pcntl_async_signals(true);
    }

    /**
     * Returns the curren process id.
     *
     * @return int
     */
    public function pid(): int
    {
        if (null === $this->pid) {
            $this->pid = posix_getpid();
        }
        return $this->pid;
    }

    /**
     * Registers a callback for some signals.
     *
     * @param array|int $signals a signal or an array of signals
     * @param callable|int $handler
     * @param bool $restartSysCalls
     */
    public function signal(array|int $signals, callable|int $handler, bool $restartSysCalls = true): void
    {
        foreach ((array)$signals as $signal) {
            pcntl_signal($signal, $handler, $restartSysCalls);
        }
    }

    /**
     * Gets the handler for a signal
     * @param int $signal
     * @return callable|int
     */
    public function getSignalHandler(int $signal): callable|int
    {
        return pcntl_signal_get_handler($signal);
    }
}