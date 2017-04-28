<?php
namespace Slince\Process\Tests\SystemV;

use PHPUnit\Framework\TestCase;
use Slince\Process\SystemV\SharedMemory;

class SharedMemoryTest extends TestCase
{
    public function setUp()
    {
        (new SharedMemory())->destroy();
    }

    public function testSet()
    {
        $memory = new SharedMemory();
        $result = $memory->set('foo', 'bar');
        $this->assertTrue($result);
    }

    public function testGet()
    {
        $memory = new SharedMemory();
        $memory->set('foo', 'bar');
        $this->assertEquals('bar', $memory->get('foo'));
    }

    public function testHas()
    {
        $memory = new SharedMemory();
        $this->assertFalse($memory->has('foo'));
        $memory->set('foo', 'bar');
        $this->assertTrue($memory->has('foo'));
    }

    public function testDelete()
    {
        $memory = new SharedMemory();
        $memory->set('foo', 'bar');
        $this->assertTrue($memory->has('foo'));
        $memory->delete('foo');
        $this->assertFalse($memory->has('foo'));
    }

    public function testClear()
    {
        $memory = new SharedMemory();
        $memory->set('foo', 'bar');
        $memory->set('bar', 'baz');
        $this->assertTrue($memory->has('foo'));
        $this->assertTrue($memory->has('bar'));
        $result = $memory->clear();
        $this->assertTrue($result);
//        var_dump($result, $memory->get('foo'));
//        $this->assertFalse($memory->has('foo'));
//        $this->assertFalse($memory->has('bar'));
    }

    public function testCheckIsEnabled()
    {
        $memory = new SharedMemory();
        $this->assertTrue($memory->isEnabled());
    }

    public function testClose()
    {
        $memory = new SharedMemory();
        $result = $memory->set('foo', 'bar');
        $this->assertTrue($result);
        $memory->close();
        $this->assertFalse($memory->isEnabled());
    }

    public function testDestroy()
    {
        $memory = new SharedMemory();
        $result = $memory->set('foo', 'bar');
        $this->assertTrue($result);
        $memory->destroy();
        $this->assertFalse($memory->isEnabled());
    }

    public function testConvertToHumanReadableSize()
    {
        $this->assertEquals(8 * 1024, SharedMemory::humanReadableToBytes('8K'));
        $this->assertEquals(8 * 1024 * 1024, SharedMemory::humanReadableToBytes('8M'));
        $this->assertEquals(8 * 1024 * 1024 * 1024, SharedMemory::humanReadableToBytes('8G'));
        $this->assertEquals(8 * 1024 * 1024 * 1024, SharedMemory::humanReadableToBytes('8g'));
    }
}