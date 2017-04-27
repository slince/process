<?php
namespace Slince\Process\Tests;

use PHPUnit\Framework\TestCase;
use Slince\Process\SignalHandler;

class SignalHandlerTest extends TestCase
{
    public function testRegister()
    {
        $signalHandler = SignalHandler::create();
        $username = '';
        $signalHandler->register(SIGUSR1, function() use (&$username) {
            $username = 'foo';
        });
        posix_kill(getmypid(), SIGUSR1);
        $this->assertEquals('foo', $username);
    }

    public function testRegisterMultiSignal()
    {
        $signalHandler = SignalHandler::create();
        $counter = 0;
        $signalHandler->register([SIGUSR1, SIGUSR2], function() use (&$username, &$counter) {
            $counter ++;
        });
        posix_kill(getmypid(), SIGUSR1);
        posix_kill(getmypid(), SIGUSR2);
        $this->assertEquals(2, $counter);
    }

    public function testGetHandler()
    {
        $signalHandler = SignalHandler::create();
        $username = '';
        $handler = function() use (&$username) {
            $username = 'foo';
        };
        $signalHandler->register(SIGUSR1, $handler);
        $handler1 = $signalHandler->getHandler(SIGUSR1);
        $this->assertTrue($handler === $handler1);
    }
}