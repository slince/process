<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\Pipe;

class DuplexFifo
{
    /**
     * The read stream
     * @var ReadableFifo
     */
    protected $readFifo;

    /**
     * The write stream,
     * @var WritableFifo
     */
    protected $writeFifo;

    public function __construct($pathname, $readBlocking = true, $writeBlocking = true)
    {
        $this->readFifo = new ReadableFifo($pathname, $readBlocking);
        $this->writeFifo = new WritableFifo($pathname, $writeBlocking);
    }

    /**
     * Sets the read fifo blocking mode
     * @param boolean $blocking
     */
    public function setReadBlocking($blocking)
    {
        $this->readFifo->setBlocking($blocking);
    }

    /**
     * Sets the read fifo blocking mode
     * @param boolean $blocking
     */
    public function setWriteBlocking($blocking)
    {
        $this->writeFifo->setBlocking($blocking);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return $this->readFifo->read();
    }

    /**
     * {@inheritdoc}
     */
    public function write($message)
    {
        return $this->writeFifo->write($message);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        is_null($this->readFifo) || $this->readFifo->close();
        is_null($this->writeFifo) || $this->writeFifo->close();
    }

    /**
     * Gets the read-only fifo
     * @return ReadableFifo
     */
    public function getReadFifo()
    {
        return $this->readFifo;
    }

    /**
     * Gets the write-only fifo
     * @return WritableFifo
     */
    public function getWriteFifo()
    {
        return $this->writeFifo;
    }

    public function __destruct()
    {
        $this->close();
    }
}
