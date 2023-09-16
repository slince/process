<?php
namespace Slince\Process\Tests\SystemV;

use PHPUnit\Framework\TestCase;
use Slince\Process\Process;
use Slince\Process\SystemV\Semaphore;

class SemaphoreTest extends TestCase
{
    public function testAcquire()
    {
        $semaphore = new Semaphore();
        $this->assertTrue($semaphore->acquire());
        $this->assertFalse($semaphore->acquire(false));
    }

    public function testRelease()
    {
        $semaphore = new Semaphore();
        $semaphore->acquire();
        $this->assertTrue($semaphore->release());
        $this->assertFalse($semaphore->release());
    }

    public function testMutex()
    {
        $process = new Process(function () {
            $semaphore = new Semaphore();
            $semaphore->acquire();
            sleep(2);
            $semaphore->release();
        });
        $process->start();
        sleep(1);
        $semaphore = new Semaphore();
        $this->assertFalse($semaphore->acquire(false));
        $process->wait();
        $this->assertTrue($semaphore->acquire(false));
        $semaphore->release();
    }

    public function testBlockingMutex()
    {
        $process = new Process(function () {
            $semaphore = new Semaphore();
            $semaphore->acquire();
            sleep(2);
            $semaphore->release();
        });
        $process->start();
        sleep(1);
        $semaphore = new Semaphore();
        $this->assertTrue($semaphore->acquire());
        $semaphore->release();
        $process->wait();
    }
}
