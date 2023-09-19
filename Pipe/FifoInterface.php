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
namespace Slince\Process\Pipe;

interface FifoInterface
{
    /**
     * Returns whether the fifo is blocking
     * @return boolean
     */
    public function isBlocking(): bool;

    /**
     * Gets the stream of the fifo.
     * @return resource
     */
    public function getStream();

    /**
     * Open ths fifo.
     *
     * @return void
     */
    public function open(): void;

    /**
     * Close the fifo.
     * @return void
     */
    public function close(): void;

    /**
     * Reads data from the fifo.
     * @param int $length The bytes that need to read.
     * @return string
     */
    public function read(int $length = 1024): string;

    /**
     * Write data to the fifo.
     * @param string $message
     */
    public function write(string $message): int;
}
