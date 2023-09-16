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

use SysvSharedMemory;

final class SharedMemory
{
    /**
     * The resource that be generated after call "shm_attach"
     * @var SysvSharedMemory
     */
    protected SysvSharedMemory $shmId;

    /**
     * The size of the shared memory
     * @var int
     */
    protected int $size;

    public function __construct(string $pathname = __FILE__, ?string $size = null, int $permission = 0666)
    {
        if (!is_null($size)) {
            $this->size = SharedMemory::humanReadableToBytes($size);
        } else {
            $this->size = (int)ini_get('sysvshm.init_mem') ?: 10000;
        }
        $this->shmId = shm_attach(IpcKeyUtils::generate($pathname), $this->size, $permission);
    }

    /**
     * Gets a value from the shared memory
     * @param int $key
     * @return mixed
     */
    public function get(int $key): mixed
    {
        return shm_get_var($this->shmId, $key);
    }

    /**
     * Persists data in the shared memory
     * @param int $key
     * @param mixed $value
     * @return bool
     */
    public function set(int $key, mixed $value)
    {
        return shm_put_var($this->shmId, $key, $value);
    }

    /**
     * Delete an item from the shared memory by its key
     * @param int $key
     * @return bool
     */
    public function delete(int $key): bool
    {
        return shm_remove_var($this->shmId, $key);
    }

    /**
     * Checks whether an item exists in the shared memory
     * @param int $key
     * @return bool
     */
    public function has(int $key): bool
    {
        return shm_has_var($this->shmId, $key);
    }

    /**
     * Deletes all items
     */
    public function clear(): bool
    {
        return shm_remove($this->shmId);
    }

    /**
     * Disconnects from shared memory
     */
    public function close(): void
    {
        if ($this->shmId) {
            shm_detach($this->shmId);
        }
    }


    /**
     * Removes all items and disconnects from shared memory
     * @return void
     */
    public function destroy(): void
    {
        if ($this->shmId) {
            shm_remove($this->shmId);
            shm_detach($this->shmId);
        }
    }

    /**
     * Convert human readable file size (e.g. "10K" or "3M") into bytes
     * @link https://github.com/brandonsavage/Upload/blob/master/src/Upload/File.php#L446
     * @param string $input
     * @return int
     */
    public static function humanReadableToBytes(string $input): int
    {
        $number = (int)$input;
        $units = array(
            'b' => 1,
            'k' => 1024,
            'm' => 1048576,
            'g' => 1073741824
        );
        $unit = strtolower(substr($input, -1));
        if (isset($units[$unit])) {
            $number = $number * $units[$unit];
        }
        return $number;
    }
}
