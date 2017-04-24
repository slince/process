<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Pipe\Fifo;
use Slince\Process\Process;

class FifoTe2st extends TestCase
{
    public function testRead()
    {
        $fifo = sys_get_temp_dir() . '/test.pipe';
        $process = new Process(function() use ($fifo){
            $pipe = new Fifo($fifo);
            $pipe->write("Hello");
            $pipe->close();
        });
        $process->start();
        $process->wait();
        $pipe = new Fifo($fifo);
        $this->assertEquals('Hello', $pipe->read());
        $pipe->close();
    }

    public function testReadWithBlocking()
    {
        $fifo = sys_get_temp_dir() . '/test.pipe';
        $process = new Process(function() use ($fifo){
            sleep(2);
            $pipe = new Fifo($fifo);
            $pipe->write("Hello");
            $pipe->close();
            sleep(2);
        });
        $process->start();
        $pipe = new Fifo($fifo);
        $this->assertFalse($pipe->read());
        $this->assertEquals('Hello', $pipe->read(true));
        $process->wait();
        $pipe->close();
    }

    public function testWrite()
    {
        $fifo = sys_get_temp_dir() . '/test.pipe';
        $pipe = new Fifo($fifo);
        $pipe->write("Hello");
        $process = new Process(function() use($fifo){
            $pipe = new Fifo($fifo);
            $pipe->write($pipe->read());
            $pipe->close();
             
        });
        $process->start();
        $process->wait();
        $this->assertEquals('Hello', $pipe->read());
        $pipe->close();

    }
}
