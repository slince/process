<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Pipe\DuplexFifo;

class DuplexFifoTest extends TestCase
{
    public function testRead()
    {
        $pathname = '/tmp/test3.pipe';
        $fifo = new DuplexFifo($pathname, true, true);

    }
}