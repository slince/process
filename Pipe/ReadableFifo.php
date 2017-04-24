<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;


use Slince\Process\Exception\RuntimeException;

class ReadableFifo extends AbstractFifo
{
    /**
     * {@inheritdoc}
     */
    public function read($blocking = null)
    {
        $blocking = is_null($blocking) ? $this->blocking : $blocking;
        $stream = $this->getStream();
        if ($blocking) {
            $read = [$stream];
            $write = [];
            $except = [];
            if (stream_select($read, $write, $except, null) > 0) {
                return stream_get_contents($stream);
            }
        }
        return stream_get_contents($stream);
    }

    /**
     * {@inheritdoc}
     */
    public function write($content, $blocking = false)
    {
        throw new RuntimeException("Cannot write some data to an readable fifo");
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        $stream = parent::getStream();
        stream_set_blocking($stream,false);
        return $stream;
    }

}