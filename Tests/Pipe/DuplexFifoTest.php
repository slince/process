<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Pipe\DuplexFifo;
use Slince\Process\Pipe\ReadableFifo;
use Slince\Process\Pipe\WritableFifo;
use Slince\Process\Tests\Utils;

class DuplexFifoTest extends TestCase
{
    protected $lastPd;

    public function setUp()
    {
        @unlink('/tmp/test1.pipe');
    }

    public function testRead()
    {
        $pathname = '/tmp/test1.pipe';
        $nativeFifo = Utils::makeNativeWriteFifo($pathname);
        fwrite($nativeFifo, 'hello');

        $fifo = new DuplexFifo($pathname);
        $this->assertEquals('hello', $fifo->read());
    }

    public function testNonBlockingRead()
    {
        $pathname = '/tmp/test1.pipe';
        $this->syncExecute(sprintf("php %s %s %d", __DIR__ . '/WriteFifo.php', 'hello', 1));
        $fifo = new DuplexFifo($pathname, false);
        $this->assertEmpty($fifo->read());
    }

    public function testBlockingRead()
    {
        $pathname = '/tmp/test1.pipe';
        $this->syncExecute(sprintf("php %s %s %d", __DIR__ . '/WriteFifo.php', 'hello', 1));
        $fifo = new DuplexFifo($pathname, true);
        $this->assertEquals('hello', $fifo->read());
    }

    public function testNonBlockingWrite()
    {
        $fifo = new DuplexFifo('/tmp/test2.pipe', true, false);
        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(65500, $bytes);

        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(0, $bytes);

        $bytes = $fifo->write(str_repeat('a', 35));
        $this->assertEquals(35, $bytes);

        $fifo->close();
    }

    public function testBlockingWrite()
    {
        $fifo = new DuplexFifo('/tmp/test2.pipe', true, true);
        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(65500, $bytes);

        $this->syncExecute(sprintf("php %s %d %d", __DIR__ . '/ReadFifo.php', 65500, 1));

        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(65500, $bytes);

        $fifo->close();
    }

    public function testGetFifo()
    {
        $fifo = new DuplexFifo('/tmp/test2.pipe');
        $this->assertInstanceOf(ReadableFifo::class, $fifo->getReadFifo());
        $this->assertInstanceOf(WritableFifo::class, $fifo->getWriteFifo());
    }

    public function testClose()
    {
        $fifo = new DuplexFifo('/tmp/test2.pipe');
        $fifo->close();
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