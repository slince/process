<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;


use Slince\Process\Exception\RuntimeException;

class WriteableFifo extends AbstractFifo
{
    /**
     * {@inheritdoc}
     */
    public function read($blocking = null)
    {
        throw new RuntimeException("Cannot write some data to an readable fifo");
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