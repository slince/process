# Process library

[![Build Status](https://img.shields.io/travis/slince/process/master.svg?style=flat-square)](https://travis-ci.org/slince/process)
[![Coverage Status](https://img.shields.io/codecov/c/github/slince/process.svg?style=flat-square)](https://codecov.io/github/slince/process)
[![Latest Stable Version](https://img.shields.io/packagist/v/slince/process.svg?style=flat-square&label=stable)](https://packagist.org/packages/slince/process)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/slince/process.svg?style=flat-square)](https://scrutinizer-ci.com/g/slince/process/?branch=master)

The library help to work with processes. It provides a more readable api and various modes for IPC via pipe(FIFO) and system v. 

# Installation

Install via composer

```
composer require slince/process
```

# Dependencies

The library replies on the following php's extension.

- ext-pcntl. Provides control processes (MUST)
- ext-sysvshm. Porvides system v shared memory (OPTIONAL)
- ext-sysvsem. Porvides system v semaphore (OPTIONAL)
- ext-sysmsg. Porvides system v message queue (OPTIONAL)

# Usage

Basic usage like this
```
$process = new Slince\Process\Process(function(){
    echo 'hello, my pid is ' . getmypid();
});
$process->start();

var_dump($process->isRunning()); // echo true
var_dump($process->getPid()); // will output the pid of child process
//do something other

$process->wait(); //waiting for the process to exit 
```
Sends signal to the process

>Note: If your php version is less than 7.1, please add the statement `declare(ticks=1);` at the beginning of the file:

```
$process = new Slince\Process\Process(function(){
    echo 'hello, my pid is ' . getmypid();
});
$process->getSignalHandler()->register([SIGUSR1, SIGUSR2], function(){
    echo 'trigger signal';
});
$process->start();
$process->signal(SIGUSER1);
//do something
$process->wait();
```

Shared memory

```
$memory = new Slince\Process\SystemV\SharedMemory();
$memory->set('foo', 'bar');
var_dump($memory->get('foo'));
```
The default size of shared memory is the sysvshm.init_mem in the php.ini, otherwise 10000 bytes. You can adjust this.

```
$memory = new Slince\Process\SystemV\SharedMemory(__FILE__, '5M'); //Adjusts to 5m
```

Semaphore
```
$semaphore = new Slince\Process\SystemV\Semaphore();
$semaphore->acquire(); //Acquires a lock
// do something
$semaphore->release() //Releases a lock
```

Message queue

```
$queue  = new Slince\Process\SystemV\MessageQueue();
$queue->send('hello');
echo $queue->receive(); //Will output hello
```

Fifo

```
$writeFifo = new Slince\Process\Pipe\WritableFifo('/tmp/test.pipe');
$writeFifo->write('some message');
$readFifo = new Slince\Process\Pipe\ReadableFifo('/tmp/test.pipe');
echo $readFifo->read();
```
Fifo works with half duplex mode. You can use `DuplexFifo` that will create two fifos.

```
$fifo = new Slince\Process\Pipe\DuplexFifo('/tmp/test.pipe');
$fifo->write('some message');
$fifo->read();
```

