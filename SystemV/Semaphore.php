<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\SystemV;

use Slince\Process\Exception\InvalidArgumentException;
use Slince\Process\Exception\RuntimeException;

class Semaphore
{
    use IpcKeyTrait;

    /**
     * Whether the semaphore is locked
     * @var boolean
     */
    protected $locked;

    /**
     * The resource that can be used to access the System V semaphore
     * @var resource
     */
    protected $semId;

    public function __construct($pathname = __FILE__, $maxAcquireNum = 1, $permission = 0666)
    {
        $ipcKey = $this->generateIpcKey($pathname);
        if (!($this->semId = sem_get($ipcKey, $maxAcquireNum, $permission))) {
            throw new RuntimeException("Cannot get semaphore identifier");
        }
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
            $result = sem_acquire($this->semId, !$blocking);
        } else {
            $result = sem_acquire($this->semId);
        }
        if ($result) {
            $this->locked = true;
        }
        return $result;
    }

    /**
     * Release the lock
     * @return bool
     */
    public function release()
    {
        if ($this->locked && sem_release($this->semId)) {
            $this->locked = false;
            return true;
        }
        return false;
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