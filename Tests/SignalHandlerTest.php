<?php
namespace Slince\Process\Tests;

use PHPUnit\Framework\TestCase;
use Slince\Process\SignalHandler;
use Slince\Process\SystemV\SharedMemory;

class SignalHandlerTest extends TestCase
{
    protected $counter = 0;

    protected $username;

    public function testRegister()
    {
        $signalHandler = SignalHandler::create();
        $memory = new SharedMemory();
        $signalHandler->register(SIGUSR1, function() use($memory){
            $this->username = 'foo';
            $memory->set('foo',  'bar');
        });
        posix_kill(getmypid(), SIGUSR1);
        usleep(100);
        $this->assertEquals('bar', $memory->get('foo'));
        $this->assertEquals('foo', $this->username);
    }

    public function testRegisterMultiSignal()
    {
        $signalHandler = SignalHandler::create();
        $signalHandler->register([SIGUSR1, SIGUSR2], function(){
            $this->counter ++;
        });
        posix_kill(getmypid(), SIGUSR1);
        posix_kill(getmypid(), SIGUSR2);
        usleep(100);
        $this->assertEquals(2, $this->counter);
    }

    public function testGetHandler()
    {
        $signalHandler = SignalHandler::create();
        $handler = function(){
            $this->username = 'foo';
        };
        $signalHandler->register(SIGUSR1, $handler);
        $handler1 = $signalHandler->getHandler(SIGUSR1);
        $this->assertTrue($handler === $handler1);
    }
}