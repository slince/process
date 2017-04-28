<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process;

use Slince\Process\Exception\InvalidArgumentException;

class SignalHandler
{
    /**
     * @var SignalHandler
     */
    protected static $instance;

    /**
     * The handlers
     * @var array
     */
    protected $handlers = [];

    public function __construct()
    {
        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
        }
    }

    /**
     * Registers a callback for some signals
     * @param int|array $signals a signal or an array of signals
     * @param callable|int $handler
     * @return SignalHandler
     */
    public function register($signals, $handler)
    {
        if (!is_array($signals)) {
            $signals = [$signals];
        }
        foreach ($signals as $signal) {
            $this->setSignalHandler($signal, $handler);
        }
        return $this;
    }

    /**
     * Register a callback for
     * @param $signal
     * @param int|callable $handler
     */
    protected function setSignalHandler($signal, $handler)
    {
        if (!is_int($handler) && !is_callable($handler)) {
            throw new InvalidArgumentException('The signal handler should be called or a number');
        }
        $this->handlers[$signal] = $handler;
        pcntl_signal($signal, $handler);
    }

    /**
     * Gets the handler for a signal
     * @param $signal
     * @return int|string
     */
    public function getHandler($signal)
    {
        if (function_exists('pcntl_signal_get_handler')) {
            return pcntl_signal_get_handler($signal);
        }
        return isset($this->handlers[$signal]) ? $this->handlers[$signal] : null;
    }


    /**
     * Gets a handler
     * @return SignalHandler
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
