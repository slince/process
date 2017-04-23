<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;

use Slince\Process\Exception\InvalidArgumentException;
use Slince\Process\Exception\RuntimeException;

class Fifo implements PipeInterface
{
    /**
     * The path of fifo
     * @var string
     */
    protected $pathname;

    /**
     * The read stream
     * @var resource
     */
    protected $readStream;

    /**
     * The write strea,
     * @var resource
     */
    protected $writeStream;

    public function __construct($pathname, $mode  = 0666)
    {
        if (($isExisted = file_exists($pathname)) && filetype($pathname) !== 'fifo') {
            throw new InvalidArgumentException("The file already exists, but is not a valid fifo file");
        } 
        if (!$isExisted && !posix_mkfifo($pathname, $mode)) {
            throw new RuntimeException("Cannot create the fifo file");
        }
        $this->pathname = $pathname;
    }

    /**
     * {@inheritdoc}
     */
    public function read($size = 1024, $blocking = false)
    {
        $stream = $this->getReadStream();
        stream_set_blocking($stream, $blocking);
        return fread($stream, $size);
    }

    /**
     * {@inheritdoc}
     */
    public function write($message, $blocking = false)
    {
        $stream = $this->getWriteStream();
        stream_set_blocking($stream, $blocking);
        return fwrite($stream, $message, strlen($message));
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        @fclose($this->readStream);
        @fclose($this->writeStream);
    }

    /**
     * Gets the read stream
     * @return bool|resource
     */
    protected function getReadStream()
    {
        if (!is_null($this->readStream)) {
            return $this->readStream;
        }
        return $this->readStream = fopen($this->pathname,  'r+');
    }

    /**
     * Gets the write stream
     * @return bool|resource
     */
    protected function getWriteStream()
    {
        if (!is_null($this->writeStream)) {
            return $this->writeStream;
        }
        return $this->writeStream = fopen($this->pathname,  'w+');
    }

    public function __destruct()
    {
        $this->close();
    }
}
