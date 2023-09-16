<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Exception\RuntimeException;
use Slince\Process\Pipe\ReadableFifo;
use Slince\Process\Pipe\WritableFifo;
use Slince\Process\Process;
use Slince\Process\Tests\Utils;

class ReadableFifoTest extends TestCase
{
    protected $lastPd;

    public function setUp(): void
    {
        file_exists('/tmp/test1.pipe') && unlink('/tmp/test1.pipe');
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
        $process = new Process(function () {
            sleep(2);
            $fifo = new WritableFifo('/tmp/test1.pipe', true);
            $fifo->write("hello");
        });
        $process->start();
        echo 'started';
        $fifo = new ReadableFifo('/tmp/test1.pipe', false);
        $this->assertEmpty($fifo->read());
        $process->wait();
    }

    public function testBlockingRead()
    {
        $process = new Process(function () {
            $fifo = new WritableFifo('/tmp/test1.pipe', true);
            $fifo->write("hello");
        });
        $process->start();
        $fifo = new ReadableFifo('/tmp/test1.pipe', true);
        $this->assertEquals('hello', $fifo->read());
        $process->wait();
    }

    public function testWrite()
    {
        $fifo = new ReadableFifo('/tmp/test1.pipe');
        $this->expectException(RuntimeException::class);
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
