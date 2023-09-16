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

use Slince\Process\Exception\RuntimeException;

class ReadableFifo extends AbstractFifo
{
    public function __construct(string $pathname, bool $blocking = true, string $mode = 'r', int $permission = 0666)
    {
        parent::__construct($pathname, $blocking, $mode, $permission);
    }

    /**
     * {@inheritdoc}
     */
    public function read(): string
    {
        $stream = $this->getStream();
        return stream_get_contents($stream);
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $message): int
    {
        throw new RuntimeException("Cannot write some data to an write-only fifo");
    }
}
