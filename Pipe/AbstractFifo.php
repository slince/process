<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;

use Slince\Process\Exception\InvalidArgumentException;
use Slince\Process\Exception\RuntimeException;

abstract class AbstractFifo implements PipeInterface
{
    protected $pathname;

    protected $mode;

    protected $permission;

    protected $stream;

    protected $blocking;

    public function __construct($pathname, $blocking, $mode, $permission = 0666)
    {
        if (($isExisted = file_exists($pathname)) && filetype($pathname) !== 'fifo') {
            throw new InvalidArgumentException("The file already exists, but is not a valid fifo file");
        }
        if (!$isExisted && !posix_mkfifo($pathname, $mode)) {
            throw new RuntimeException("Cannot create the fifo file");
        }
        $this->pathname = $pathname;
        $this->blocking = (boolean)$blocking;
        $this->mode = $mode;
        $this->permission = $permission;
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        if (!is_null($this->stream)) {
            return $this->stream;
        }
        return $this->stream = fopen($this->pathname, $this->mode);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        is_resource($this->stream) && fclose($this->stream);
    }

    /**
     * Sets the blocking mode
     * @param boolean $blocking
     */
    public function setBlocking($blocking)
    {
        $this->blocking = $blocking;
    }
}