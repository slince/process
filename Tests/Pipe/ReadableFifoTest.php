<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
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
        $writeFifo = $this->makeWriteFifo();
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

    protected function makeWriteFifo()
    {
        $pathname = '/tmp/test1.pipe';
        if (!file_exists($pathname)) {
            posix_mkfifo($pathname, 0666);
        }
        $fifo = fopen($pathname, 'w+');
        return $fifo;
    }

    protected function syncExecute($command)
    {
        $this->lastPd = popen($command, 'r');
        stream_set_blocking($this->lastPd, false);
        return $this->lastPd;
    }

    public function tearDown()
    {
        is_resource($this->lastPd) && pclose($this->lastPd);
        @unlink('/tmp/test1.pipe');
    }
}