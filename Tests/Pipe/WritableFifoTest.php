<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Pipe\WritableFifo;
use Slince\Process\Tests\Utils;

class WritableFifoTest extends TestCase
{
    protected $lastPd;

    public function setUp(): void
    {
        file_exists('/tmp/test2.pipe') && unlink('/tmp/test2.pipe');
    }

    public function testSimpleWrite()
    {
        $fifo = new WritableFifo('/tmp/test2.pipe');
        $fifo->write('hello');
        $readFifo = Utils::makeNativeReadFifo('/tmp/test2.pipe');
        $this->assertEquals('hello', fread($readFifo, 5));
        $fifo->close();
    }

    public function testNonBlockingWrite()
    {
        $fifo = new WritableFifo('/tmp/test2.pipe', false);
        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(65500, $bytes);

        $this->syncExecute(sprintf("php %s %d %d", __DIR__ . '/ReadFifo.php', 1024, 1));

        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(0, $bytes);

        $bytes = $fifo->write(str_repeat('a', 35));
        $this->assertEquals(35, $bytes);

        $fifo->close();
    }

    public function testBlockingWrite()
    {
        $fifo = new WritableFifo('/tmp/test2.pipe', true);
        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(65500, $bytes);

        $this->syncExecute(sprintf("php %s %d %d", __DIR__ . '/ReadFifo.php', 65500, 1));

        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(65500, $bytes);

        $fifo->close();
    }

    public function testGetStream()
    {
        $fifo = new WritableFifo('/tmp/test2.pipe');
        $this->assertTrue(is_resource($fifo->getStream()));
    }

    public function testIsBlocking()
    {
        $fifo = new WritableFifo('/tmp/test2.pipe');
        $this->assertTrue($fifo->isBlocking());
    }

    protected function syncExecute($command): void
    {
        $this->lastPd = Utils::asyncExecute($command);
    }

    public function tearDown(): void
    {
        is_resource($this->lastPd) && pclose($this->lastPd);
    }
}
