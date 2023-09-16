<?php
namespace Slince\Process\Tests;

use PHPUnit\Framework\TestCase;
use Slince\Process\Process;
use Slince\Process\ProcessInterface;

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
            while (true) {
                echo 'hehe', PHP_EOL;
            }
        });
        $process->start();
        $process->stop();

        $this->assertTrue($process->isStopped());
        $this->assertEquals(SIGSTOP, $process->getStopSignal());
    }

    public function testIfSignaled()
    {
        $process = new Process(function () {
            sleep(1);
        });
        $process->start();
        $process->terminate();
        sleep(1);
        $this->assertTrue($process->isTerminated());
        $this->assertEquals(SIGTERM, $process->getTermSignal());
    }

    public function testGetStatus()
    {
        $process = new Process(function () {
            sleep(1);
        });
        $this->assertEquals(ProcessInterface::STATUS_READY, $process->getStatus());
        $process->start();
        $this->assertEquals(ProcessInterface::STATUS_RUNNING, $process->getStatus());
        $process->wait();
        $this->assertEquals(ProcessInterface::STATUS_EXITED, $process->getStatus());
    }
}
