<?php
namespace Slince\Process\Tests;

use PHPUnit\Framework\TestCase;
use Slince\Process\Process;

class ProcessTest extends TestCase
{
    public function testStart()
    {
        $process = new Process(function () {
            sleep(1);
        });
        $this->assertFalse($process->isRunning());
        $process->start();
        $this->assertTrue($process->isRunning());
        $process->wait();
    }

    public function testWait()
    {
        $process = new Process(function () {
            sleep(1);
        });
        $process->start();
        $process->wait();
        $this->assertFalse($process->isRunning());
    }

    public function testGetPid()
    {
        $process = new Process(function () {
            sleep(1);
        });
        $this->assertNull($process->getPid());
        $process->start();
        $this->assertGreaterThan(0, $process->getPid());
        $process->wait();
    }

    public function testGetExitCode()
    {
        $process = new Process(function () {
            usleep(100);
        });
        $process->run();
        $this->assertTrue($process->isExited());
        $this->assertEquals(0, $process->getExitCode());
        $process = new Process(function () {
            exit(255);
        });
        $process->run();
        $this->assertTrue($process->isExited());
        $this->assertEquals(255, $process->getExitCode());
        $this->assertNotEmpty($process->getExitCodeText());
    }

    public function testIfStopped()
    {
        $process = new Process(function () {
            sleep(12);
        });
        $process->start();
        $process->stop();

        $this->assertTrue($process->isStopped());
        var_dump($process->getStatus());
        $this->assertEquals(SIGSTOP, $process->getStopSignal());

//        $process->continue();
        $this->assertTrue($process->hasBeenContinued());
        $process->wait();
    }

    public function testIfSignaled()
    {
        $process = new Process(function () {
            sleep(1);
        });
        $process->start();
        $process->stop();
        $this->assertTrue($process->isStopped());
        $this->assertEquals(SIGKILL, $process->getStopSignal());

        $process->wait();
    }

    public function testGetSignalHandler()
    {
        $process = new Process(function () {
            sleep(1);
        });
        $this->assertInstanceOf(SignalHandler::class, $process->getSignalHandler());
    }

    public function testGetStatus()
    {
        $process = new Process(function () {
            sleep(1);
        });
        $this->assertNull($process->getStatus());
        $process->run();
        $this->assertInstanceOf(Status::class, $process->getStatus());
    }
}
