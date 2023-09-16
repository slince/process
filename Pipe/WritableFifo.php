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

class WritableFifo extends AbstractFifo
{
    public function __construct(string $pathname, bool $blocking = true, string $mode = 'w+', int $permission = 0666)
    {
        parent::__construct($pathname, $blocking, $mode, $permission);
    }

    /**
     * {@inheritdoc}
     */
    public function read(): string
    {
        throw new RuntimeException("Cannot read data from an write-only fifo");
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $message): int
    {
        $stream = $this->getStream();
        if (false === ($bytes = fwrite($stream, $message, strlen($message)))) {
            throw new RuntimeException(sprintf('Cannot write message to the fifo "%s"', $this->pathname));
        }
        return $bytes;
    }
}
