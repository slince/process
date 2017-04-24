<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Pipe\WritableFifo;

class WritableFifoTest extends TestCase
{
    protected $lastPd;

    public function setUp()
    {
        @unlink('/tmp/test2.pipe');
    }

    public function testSimpleWrite()
    {
        $fifo = new WritableFifo('/tmp/test2.pipe');
        $fifo->write('hello');
        $readFifo = $this->makeReadFifo();
        $this->assertEquals('hello', fread($readFifo, 5));
        $fifo->close();
    }

    public function testNonBlockingWrite()
    {
        $fifo = new WritableFifo('/tmp/test3.pipe', false);
        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(65500, $bytes);

        $this->syncExecute(sprintf("php %s %d %d", __DIR__ . '/ReadFifo.php', 1024, 2));

        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(0, $bytes);

        $bytes = $fifo->write(str_repeat('a', 35));
        $this->assertEquals(35, $bytes);

        $fifo->close();
    }

    public function tes2tBlockingWrite()
    {
        $fifo = new WritableFifo('/tmp/test4.pipe', true);
        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(65500, $bytes);

        $this->syncExecute(sprintf("php %s %d %d", __DIR__ . '/ReadFifo.php', 1024, 2));

        $bytes = $fifo->write(str_repeat('a', 65500));
        $this->assertEquals(65500, $bytes);

        $fifo->close();
    }

    protected function syncExecute($command)
    {
        $this->lastPd = popen($command, 'r');
        stream_set_blocking($this->lastPd, false);
        return $this->lastPd;
    }

    protected function makeReadFifo()
    {
        $pathname = '/tmp/test2.pipe';
        if (!file_exists($pathname)) {
            posix_mkfifo($pathname, 0666);
        }
        $fifo = fopen($pathname, 'r+');
        return $fifo;
    }
}