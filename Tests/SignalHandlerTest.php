<?php
namespace Slince\Process\Tests;

use PHPUnit\Framework\TestCase;
use Slince\Process\SignalHandler;

class SignalHandlerTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        if (!function_exists('pcntl_async_signals')) {
            declare(ticks = 1);
        }
    }

    public function testRegister()
    {
        $signalHandler = SignalHandler::getInstance();
        $username = '';
        $signalHandler->register(SIGUSR1, function () use (&$username) {
            $username = 'foo';
        });
        posix_kill(getmypid(), SIGUSR1);
        $this->assertEquals('foo', $username);
    }

    public function testRegisterMultiSignal()
    {
        $signalHandler = SignalHandler::getInstance();
        $counter = 0;
        $signalHandler->register([SIGUSR1, SIGUSR2], function () use (&$counter) {
            $counter ++;
        });
        posix_kill(getmypid(), SIGUSR1);
        posix_kill(getmypid(), SIGUSR2);
        $this->assertEquals(2, $counter);
    }

    public function testGetHandler()
    {
        $signalHandler = SignalHandler::getInstance();
        $handler = function () {
            echo 'signal';
        };
        $signalHandler->register(SIGUSR1, $handler);
        $handler1 = $signalHandler->getHandler(SIGUSR1);
        $this->assertTrue($handler === $handler1);
    }
}