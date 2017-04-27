<?php
namespace Slince\Process\Tests;


use PHPUnit\Framework\TestCase;
use Slince\Process\Status;

class StatusTest extends TestCase
{
    public function setUp()
    {
        pcntl_signal(SIGUSR1, SIG_DFL);
    }

    public function testExit()
    {
        $pid = pcntl_fork();
        if ($pid > 0) {
            pcntl_wait($status);
            $status = new Status($status);
            $this->assertTrue($status->isExited());
            $this->assertEquals(2, $status->getExitCode());
            $this->assertNotEmpty($status->getErrorMessage());
        } elseif ($pid == 0) {
            usleep(100);
            if (!file_exists('/file-not-exists')) {
                exit(2);
            }
            exit;
        }
    }

    public function testSignal()
    {
        $pid = pcntl_fork();
        if ($pid > 0) {
            posix_kill($pid, SIGUSR1);
            pcntl_wait($status);
            $status = new Status($status);
            $this->assertTrue($status->isSignaled());
            $this->assertEquals(SIGUSR1, $status->getTerminateSignal());
        } elseif ($pid == 0) {
            usleep(100);
            exit;
        }
    }

    public function testIsSuccessful()
    {
        $pid = pcntl_fork();
        if ($pid > 0) {
            pcntl_wait($status);
            $status = new Status($status);
            $this->assertFalse($status->isSuccessful());
        } elseif ($pid == 0) {
            usleep(100);
            exit(2);
        }
        sleep(1);
        $pid = pcntl_fork();
        if ($pid > 0) {
            pcntl_wait($status2);
            $status = new Status($status2);
            $this->assertTrue($status->isSuccessful());
        } elseif ($pid == 0) {
            usleep(100);
            exit;
        }
    }

    public function testStop()
    {

    }
}