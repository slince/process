<?php
namespace Slince\Process\Tests;

use PHPUnit\Framework\TestCase;
use Slince\Process\SignalHandler;

class SignalHandlerTest extends TestCase
{
    protected $counter = 0;

    protected $username;

    public function testRegister()
    {
        $signalHandler = SignalHandler::create();
        $signalHandler->register(SIGUSR1, function(){
            $this->username = 'foo';
        });
        usleep(100);
        posix_kill(getmypid(), SIGUSR1);
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