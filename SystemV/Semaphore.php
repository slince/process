<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\SystemV;

use Slince\Process\Exception\InvalidArgumentException;

class Semaphore
{
    use IpcKeyTrait;

    /**
     * The resource that can be used to access the System V semaphore
     * @var resource
     */
    protected $semId;

    public function __construct($pathname = __FILE__)
    {
        $ipcKey = $this->generateIpcKey($pathname);
        $this->semId = sem_get($ipcKey);
    }

    /**
     * Acquires the lock
     * @param bool $blocking
     * @return bool
     */
    public function acquire($blocking = true)
    {
        //non-blocking requires php version greater than 5.6.1
        if (!$blocking) {
            if (version_compare(PHP_VERSION, '5.6.1') < 0) {
                throw new InvalidArgumentException("Semaphore requires php version greater than 5.6.1 when using blocking");
            }
            return sem_acquire($this->semId, !$blocking);
        }
        return sem_acquire($this->semId);
    }

    /**
     * Release the lock
     * @return bool
     */
    public function release()
    {
        return sem_release($this->semId);
    }

    /**
     * Destroy semaphore
     * @return void
     */
    public function destroy()
    {
        sem_remove($this->semId);
    }

    public function __destruct()
    {
        $this->destroy();
    }
}