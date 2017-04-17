<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;

use Slince\Process\Exception\InvalidArgumentException;
use Slince\Process\Exception\RuntimeException;

class Pipe implements PipeInterface
{
    /**
     * The path of fifo
     * @var string
     */
    protected $fifoPath;

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

    public function __construct($fifoPath, $mode  = 0666)
    {
        if (file_exists($fifoPath) && gettype($fifoPath) !== 'fifo') {
            throw new InvalidArgumentException("The file already exists, but not the valid fifo file");
        }
        if (!posix_mkfifo($fifoPath, $mode))  {
            throw new RuntimeException("Cannot create the fifo file");
        }
        $this->fifoPath = $fifoPath;
    }

    /**
     * {@inheritdoc}
     */
    public function read($blocking = false)
    {
        $stream = $this->getReadStream();
        stream_set_blocking($stream, $blocking);
        return stream_get_contents($stream);
    }

    /**
     * {@inheritdoc}
     */
    public function write($content, $blocking =  false)
    {
        $stream = $this->getWriteStream();
        stream_set_blocking($stream, $blocking);
        return fwrite($stream, $content, strlen($content));
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        @fclose($this->readStream);
        @fclose($this->writeStream);
    }

    protected function getReadStream()
    {
        if (!is_null($this->readStream)) {
            return $this->readStream;
        }
        return $this->readStream = fopen($this->fifoPath,  'r+');
    }

    protected function getWriteStream()
    {
        if (!is_null($this->writeStream)) {
            return $this->writeStream;
        }
        return $this->writeStream = fopen($this->fifoPath,  'w+');
    }

    public function __destruct()
    {
        $this->close();
    }
}