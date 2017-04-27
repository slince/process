<?php
namespace Slince\Process\Tests\SystemV;

use PHPUnit\Framework\TestCase;
use Slince\Process\Process;
use Slince\Process\SystemV\MessageQueue;

class MessageQueueTest extends TestCase
{
    public function testSend()
    {
        $queue  = new MessageQueue();
        $this->assertTrue($queue->send('hello'));
    }

    public function testReceive()
    {
        $process = new Process(function () {
            $queue = new MessageQueue();
            $queue->send('hello');
        });
        $process->start();
        $process->wait();
        $queue = new MessageQueue();
        $this->assertEquals('hello', $queue->receive());
    }

    public function testBlockingReceive()
    {
        $this->markTestSkipped();
        $process = new Process(function () {
            sleep(2);
            $queue = new MessageQueue();
            $queue->send('hello');
        });
        $process->start();
        $queue = new MessageQueue();
        echo $queue->receive(false);
        echo $queue->receive(false);
        echo 'start';
        $this->assertFalse($queue->receive(false));
        echo 'end';
        $this->assertEquals('hello', $queue->receive(true));
        $process->wait();
    }

    public function testGetState()
    {
        $queue = new MessageQueue();
        $this->assertNotEmpty($queue->getState());
    }

    public function testSetState()
    {
        $queue = new MessageQueue();
        $mode = $queue->getState()['msg_perm.mode'];
        $queue->setState('msg_perm.mode', $mode + 1);
        $this->assertEquals($mode + 1, $queue->getState()['msg_perm.mode']);
    }
}