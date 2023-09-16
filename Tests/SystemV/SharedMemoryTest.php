<?php
namespace Slince\Process\Tests\SystemV;

use PHPUnit\Framework\TestCase;
use Slince\Process\SystemV\SharedMemory;

class SharedMemoryTest extends TestCase
{
    public function setUp(): void
    {
        (new SharedMemory())->destroy();
    }

    public function testSet()
    {
        $memory = new SharedMemory();
        $result = $memory->set(0, 'bar');
        $this->assertTrue($result);
        $memory->destroy();
    }

    public function testGet()
    {
        $memory = new SharedMemory();
        $memory->set(0, 'bar');
        $this->assertEquals('bar', $memory->get(0));
    }

    public function testHas()
    {
        $memory = new SharedMemory();
        var_dump($memory->get(0));

        $this->assertFalse($memory->has(0));
        $memory->set(0, 'bar');
        $this->assertTrue($memory->has(0));
    }

    public function testDelete()
    {
        $memory = new SharedMemory();
        $memory->set(0, 'bar');
        $this->assertTrue($memory->has(0));
        $memory->delete(0);
        $this->assertFalse($memory->has(0));
    }

    public function testClear()
    {
        $memory = new SharedMemory();
        $memory->set(0, 'bar');
        $memory->set(1, 'baz');
        $this->assertTrue($memory->has(0));
        $this->assertTrue($memory->has(1));
        $result = $memory->clear();
        $this->assertTrue($result);
    }

    public function testClose()
    {
        $memory = new SharedMemory();
        $result = $memory->set(0, 'bar');
        $this->assertTrue($result);
        $memory->close();
    }

    public function testDestroy()
    {
        $memory = new SharedMemory();
        $result = $memory->set(0, 'bar');
        $this->assertTrue($result);
        $memory->destroy();
    }

    public function testConvertToHumanReadableSize()
    {
        $this->assertEquals(8 * 1024, SharedMemory::humanReadableToBytes('8K'));
        $this->assertEquals(8 * 1024 * 1024, SharedMemory::humanReadableToBytes('8M'));
        $this->assertEquals(8 * 1024 * 1024 * 1024, SharedMemory::humanReadableToBytes('8G'));
        $this->assertEquals(8 * 1024 * 1024 * 1024, SharedMemory::humanReadableToBytes('8g'));
    }
}
