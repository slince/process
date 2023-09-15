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

final class MessageQueue
{
    /**
     * The resource that can be used to access to the system v message queue
     * @var resource
     */
    protected $mqId;

    public function __construct(string $pathname = __FILE__, int $mode = 0666)
    {
        $this->mqId = msg_get_queue(IpcKeyUtils::generate($pathname), $mode);
    }

    /**
     * Sets information for the queue
     * @param array $states
     * @return bool
     */
    public function setStates(array $states): bool
    {
        return msg_set_queue($this->mqId, $states);
    }

    /**
     * Sets information for the queue by specifying the key and value
     * @param string $key
     * @param string|int $value
     * @return bool
     */
    public function setState(string $key, mixed $value): bool
    {
        return $this->setStates([$key => $value]);
    }

    /**
     * Gets the information of the queue
     *
     * The return value is an array whose keys and values have the following meanings:
     * Array structure for msg_stat_queue
     * msg_perm.uid The uid of the owner of the queue.
     * msg_perm.gid The gid of the owner of the queue.
     * msg_perm.mode The file access mode of the queue.
     * msg_stime The time that the last message was sent to the queue.
     * msg_rtime The time that the last message was received from the queue.
     * msg_ctime The time that the queue was last changed.
     * msg_qnum The number of messages waiting to be read from the queue.
     * msg_qbytes The maximum number of bytes allowed in one message queue. On Linux, this value may be read and modified via /proc/sys/kernel/msgmnb.
     * msg_lspid The pid of the process that sent the last message to the queue.
     * msg_lrpid The pid of the process that received the last message from the queue.
     *
     * @return array
     */
    public function getState(): array
    {
        return msg_stat_queue($this->mqId);
    }

    /**
     * Sends the message to the queue
     * @param string $message
     * @param int $messageType
     * @param bool $blocking
     * @param bool $unserialize
     */
    public function send(string $message, int $messageType, bool $blocking = true, bool $unserialize = false): void
    {
        if (!msg_send(
            $this->mqId,
            $messageType,
            $message,
            $unserialize,
            $blocking,
            $errorCode
        )
        ) {
            throw new RuntimeException("Failed to send the message to the queue", $errorCode);
        }
    }

    /**
     * Gets the message from the queue
     * @param int $desiredMessageType
     * @param bool $blocking
     * @param int $maxSize The max size you want receive(Unit:bytes)
     * @param bool $unserialize
     * @return string|null
     */
    public function receive(int $desiredMessageType, bool $blocking = true, int $maxSize = 10240, bool $unserialize = false): ?string
    {
        $flags = $blocking ? 0 : MSG_IPC_NOWAIT;
        if (!msg_receive(
            $this->mqId,
            $desiredMessageType,
            $realMessageType,
            $maxSize,
            $message,
            $unserialize,
            $flags,
            $errorCode
        )) {
            throw new RuntimeException("Failed to receive message from the queue", $errorCode);
        }
        return $message;
    }
}
