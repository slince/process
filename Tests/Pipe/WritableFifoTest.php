<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Pipe\WritableFifo;

class WritableFifoTest extends TestCase
{
    public function setUp(): void
    {
        file_exists('/tmp/test2.pipe') && unlink('/tmp/test2.pipe');
    }

    public function testIsBlocking()
    {
        $fifo = new WritableFifo('/tmp/test2.pipe');
        $this->assertTrue($fifo->isBlocking());
    }
}
