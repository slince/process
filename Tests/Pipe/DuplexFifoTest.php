<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Pipe\DuplexFifo;

class DuplexFifoTest extends TestCase
{
    public function testRead()
    {
        $pathname = '/tmp/test3.pipe';
        $nativeFifo = FifoUtils::makeNativeWriteFifo($pathname);
        fwrite($nativeFifo, 'hello');

        $fifo = new DuplexFifo($pathname);
        $this->assertEquals('hello', $fifo->read());
    }

    public function testNonBlockingRead()
    {

    }
}