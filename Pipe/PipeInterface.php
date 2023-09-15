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

interface PipeInterface
{
    /**
     * Reads data from the pipe
     * @return string
     */
    public function read(): string;

    /**
     * Write data to the pipe
     * @param string $message
     */
    public function write(string $message): void;

    /**
     * Gets the stream of the pipe
     * @return resource
     */
    public function getStream();

    /**
     * Close the pipe
     * @return void
     */
    public function close(): void;

    /**
     * Returns whether the pipe is blocking
     * @return boolean
     */
    public function isBlocking(): bool;
}
