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

use Slince\Process\Exception\InvalidArgumentException;
use Slince\Process\Exception\RuntimeException;

abstract class AbstractFifo implements FifoInterface
{
    protected string $pathname;
    protected string $mode;
    protected int $permission;
    protected $stream;
    protected bool $blocking;

    public function __construct(string $pathname, bool $blocking, string $mode, int $permission = 0666)
    {
        if (($exists = file_exists($pathname)) && filetype($pathname) !== 'fifo') {
            throw new InvalidArgumentException("The file already exists, but is not a valid fifo file");
        }
        if (!$exists && !posix_mkfifo($pathname, $permission)) {
            throw new RuntimeException("Cannot create the fifo file");
        }
        $this->pathname = $pathname;
        $this->blocking = $blocking;
        $this->mode = $mode;
        $this->permission = $permission;
    }

    /**
     * {@inheritdoc}
     */
    public function isBlocking(): bool
    {
        return $this->blocking;
    }

    /**
     * Open the fifo.
     *
     * @return void
     */
    public function open(): void
    {
        $this->stream = fopen($this->pathname, $this->mode);
        if (!$this->blocking) {
            if (false === stream_set_blocking($this->stream, false)) {
                throw new RuntimeException('Cannot set steam to non blocking');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        is_resource($this->stream) && fclose($this->stream);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        if (null !== $this->stream) {
            return $this->stream;
        }
        $this->open();
        return $this->stream;
    }
}
