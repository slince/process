<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Pipe\Pipe;
use Slince\Process\Process;

class PipeTest extends TestCase
{
    public function testRead()
    {
        $fifo = sys_get_temp_dir() . '/test.pipe';
        $process = new Process(function() use ($fifo){
            $pipe = new Pipe($fifo);
            $pipe->write("Hello");
            $pipe->close();
        });
        $process->start();
        $process->wait();
        $pipe = new Pipe($fifo);
        $this->assertEquals('Hello', $pipe->read());
        $pipe->close();
    }

    public function testReadWithBlocking()
    {
        $fifo = sys_get_temp_dir() . '/test.pipe';
        $process = new Process(function() use ($fifo){
            sleep(2);
            $pipe = new Pipe($fifo);
            $pipe->write("Hello");
            $pipe->close();
            sleep(2);
        });
        $process->start();
        $pipe = new Pipe($fifo);
        $this->assertFalse($pipe->read());
        $this->assertEquals('Hello', $pipe->read(true));
        $process->wait();
        $pipe->close();
    }

    public function testWrite()
    {
        $fifo = sys_get_temp_dir() . '/test.pipe';
        $pipe = new Pipe($fifo);
        $pipe->write("Hello");
        ob_start();
        $process = new Process(function() use($fifo){
            $pipe = new Pipe($fifo);
            echo $pipe->read();
        });
        $process->start();
        $process->wait();
        $content = ob_get_clean();
        $this->assertEquals('Hello', $content);
    }
}