<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\SystemV;

use Slince\Process\Exception\RuntimeException;

class MessageQueue
{
    use IpcKeyTrait;

    /**
     * The resource that can be used to access to the system v message queue
     * @var resource
     */
    protected $mqId;

    /**
     * The message type
     * @var int
     */
    protected $messageType;

    protected $unserialize = true;

    public function __construct($messageType = 1, $pathname = __FILE__, $mode = 0666)
    {
        $this->messageType = $messageType;
        $ipcKey = $this->generateIpcKey($pathname);
        $this->mqId = msg_get_queue($ipcKey, $mode);
    }

    /**
     * Sends the message to the queue
     * @param string $message
     * @param bool $blocking
     * @return bool
     */
    public function send($message, $blocking = true)
    {
        if (!msg_send(
            $this->mqId,
            $this->messageType,
            $message,
            $this->unserialize,
            $blocking,
            $errorCode
        )
        ) {
            throw new RuntimeException("Failed to send the message to the queue", $errorCode);
        }
        return true;
    }

    /**
     * Gets the message from the queue
     * @param bool $blocking
     * @param int $maxSize The max size you want receive(Unit:bytes)
     * @return string|false
     */
    public function receive($blocking = true, $maxSize = 10240)
    {
        $flags = $blocking ? 0 : MSG_IPC_NOWAIT;
        if (msg_receive(
            $this->mqId,
            $this->messageType,
            $realMessageType,
            $maxSize,
            $message,
            $this->unserialize,
            $flags,
            $errorCode
        )
        ) {
            return $message;
        }
        return false;
    }

    /**
     * Sets information for the queue
     * @param array $states
     * @return bool
     */
    public function setStates(array $states)
    {
        return msg_set_queue($this->mqId, $states);
    }

    /**
     * Sets information for the queue by specifying the key and value
     * @param string $key
     * @param string|int $value
     * @return bool
     */
    public function setState($key, $value)
    {
        return $this->setStates([
            $key => $value
        ]);
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
    public function getState()
    {
        return msg_stat_queue($this->mqId);
    }
}
