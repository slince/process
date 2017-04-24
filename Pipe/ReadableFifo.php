<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;


use Slince\Process\Exception\RuntimeException;

class ReadableFifo extends AbstractFifo
{
    public function __construct($pathname, $blocking = true, $mode = 'r+', $permission = 0666)
    {
        parent::__construct($pathname, $blocking, $mode, $permission);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $stream = $this->getStream();
        if ($this->blocking) {
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
        throw new RuntimeException("Cannot write some data to an write-only fifo");
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