<?php

declare(strict_types=1);

/*
 * This file is part of the slince/process package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Slince\Process\SystemV;

use Slince\Process\Exception\RuntimeException;

final class Semaphore
{
    /**
     * Whether the semaphore is locked
     * @var boolean
     */
    protected bool $locked;

    /**
     * The resource that can be used to access the System V semaphore
     * @var resource
     */
    protected $semId;

    public function __construct(string $pathname = __FILE__, $maxAcquireNum = 1, $permission = 0666)
    {
        if (!($this->semId = sem_get(IpcKeyUtils::generate($pathname), $maxAcquireNum, $permission))) {
            throw new RuntimeException("Cannot get semaphore identifier");
        }
    }

    /**
     * Acquires the lock
     * @param bool $blocking
     * @return bool
     */
    public function acquire(bool $blocking = true): bool
    {
        $result = sem_acquire($this->semId, !$blocking);
        if ($result) {
            $this->locked = true;
        }
        return $result;
    }

    /**
     * Release the lock
     * @return bool
     */
    public function release(): bool
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
    public function destroy(): void
    {
        sem_remove($this->semId);
    }
}
