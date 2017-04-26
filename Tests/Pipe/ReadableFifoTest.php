<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Exception\RuntimeException;
use Slince\Process\Pipe\ReadableFifo;

class ReadableFifoTest extends TestCase
{
    protected $lastPd;

    public function setUp()
    {
        @unlink('/tmp/test1.pipe');
    }

    public function testSimpleRead()
    {
        $writeFifo = Utils::makeNativeWriteFifo('/tmp/test1.pipe');
        $writeBytes = fwrite($writeFifo, 'hello');
        $this->assertEquals(5, $writeBytes);
        $fifo = new ReadableFifo('/tmp/test1.pipe', false);
        $this->assertEquals('hello', $fifo->read());
    }

    public function testNonBlockingRead()
    {
        $this->syncExecute(sprintf("php %s %s %d", __DIR__ . '/WriteFifo.php', 'hello', 2));
        $fifo = new ReadableFifo('/tmp/test1.pipe', false);
        $this->assertEmpty($fifo->read());
    }

    public function testBlockingRead()
    {
        $this->syncExecute(sprintf("php %s %s %d", __DIR__ . '/WriteFifo.php', 'hello', 2));
        $fifo = new ReadableFifo('/tmp/test1.pipe', true);
        $this->assertEquals('hello', $fifo->read());
    }

    public function testWrite()
    {
        $fifo = new ReadableFifo('/tmp/test1.pipe');
        $this->setExpectedException(RuntimeException::class);
        $fifo->write('some message');
    }

    public function testGetStream()
    {
        $fifo = new ReadableFifo('/tmp/test1.pipe');
        $this->assertTrue(is_resource($fifo->getStream()));
    }

    public function testIsBlocking()
    {
        $fifo = new ReadableFifo('/tmp/test1.pipe');
        $this->assertTrue($fifo->isBlocking());
        $fifo->setBlocking(false);
        $this->assertFalse($fifo->isBlocking());
    }

    protected function syncExecute($command)
    {
        $this->lastPd = Utils::asyncExecute($command);
    }

    public function tearDown()
    {
        is_resource($this->lastPd) && pclose($this->lastPd);
        @unlink('/tmp/test1.pipe');
    }
}