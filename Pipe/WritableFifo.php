<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;

use Slince\Process\Exception\RuntimeException;

class WritableFifo extends AbstractFifo
{
    public function __construct($pathname, $blocking = true, $mode = 'w+', $permission = 0666)
    {
        parent::__construct($pathname, $blocking, $mode, $permission);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        throw new RuntimeException("Cannot read data from an write-only fifo");
    }

    /**
     * {@inheritdoc}
     */
    public function write($message)
    {
        $stream = $this->getStream();
        return fwrite($stream, $message, strlen($message));
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        $stream = parent::getStream();
        if ($this->blocking === false) {
            stream_set_blocking($stream,false);
        }
        return $stream;
    }

    public function setBlocking($blocking)
    {
        parent::setBlocking($blocking);
        if (!is_null($this->stream)) {
            stream_set_blocking($this->stream,$blocking);
        }
    }
}