<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\SignalHandler;


class SignalHandler
{
    /**
     * Registers a callback for some signals
     * @param int|array $signals a signal or an array of signals
     * @param callable $callback a callback
     * @return SignalHandler;
     */
    public function register($signals, $callback)
    {
        if (!is_array($signals)) {
            $signals = [$signals];
        }
        foreach ($signals as $signal) {
            $this->setSignalHandler($signal, $callback);
        }
        return $this;
    }

    /**
     * unregisters handlers for signals
     * @param $signals
     * @return $this
     */
    public function unregister($signals)
    {
        if (!is_array($signals)) {
            $signals = [$signals];
        }
        foreach ($signals as $signal) {
            $this->removeSignalHandler($signal);
        }
        return $this;
    }

    /**
     * Register a callback for
     * @param $signal
     * @param $callback
     */
    protected function setSignalHandler($signal, $callback)
    {
        pcntl_signal($signal, $callback);
    }

    /**
     * @param $signal
     */
    protected function removeSignalHandler($signal)
    {
        pcntl_signal($signal, SIG_DFL);
    }


    /**
     * Creates a handler
     * @param boolean $disableDeclareTicks disable declare(tickets = 1)
     * @return SignalHandler
     */
    public static function create($disableDeclareTicks = false)
    {
        $handler = new static();
        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
        } else {
            declare (ticks = 1);
        }
        return $handler;
    }
}