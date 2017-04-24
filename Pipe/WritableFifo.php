<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;

use Slince\Process\Exception\RuntimeException;

class WritableFifo extends AbstractFifo
{
    public function __construct($pathname, $blocking, $mode = 'w+', $permission = 0666)
    {
        parent::__construct($pathname, $blocking, $mode, $permission);
    }

    /**
     * {@inheritdoc}
     */
    public function read($blocking = null)
    {
        throw new RuntimeException("Cannot read data from an write-only fifo");
    }

    /**
     * {@inheritdoc}
     */
    public function write($message, $blocking = null)
    {
        $blocking = is_null($blocking) ? $this->blocking : $blocking;
        $stream = $this->getStream();
        stream_set_blocking($stream,false);
        return fwrite($stream, $message, strlen($message));
    }
}