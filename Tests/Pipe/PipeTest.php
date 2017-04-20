<?php
namespace Slince\Process\Tests\Pipe;

use PHPUnit\Framework\TestCase;
use Slince\Process\Pipe\Pipe;

class PipeTest extends TestCase
{
    public function testWrite()
    {
        $pipe = new Pipe(sys_get_temp_dir() . '/test.pipe');
        $pipe->write();
    }
}