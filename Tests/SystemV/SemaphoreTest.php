<?php
namespace Slince\Process\Tests\SystemV;

use Slince\Process\SystemV\Semaphore;

class SemaphoreTest
{
    public function testAcquire()
    {
        $semaphore = new Semaphore();
        $semaphore->acquire();
    }

}