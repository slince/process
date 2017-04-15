<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process;

use Slince\Process\Exception\InvalidArgumentException;
use Slince\Process\Exception\RuntimeException;

class Process extends ProcessInterface
{
    const STATUS_RUNNING = 'running';

    const STATUS_TERMINATED = 'terminated';

    /**
     * 需要进程执行的代码
     * @var callable
     */
    protected $callback;

    /**
     * 是否正在执行
     * @var bool
     */
    protected $isRunning;

    /**
     * 进程id
     * @var int
     */
    protected $pid;

    /**
     * 信号处理器
     * @var array
     */
    protected $signalHandlers = [];

    /**
     * 状态
     * @var string
     */
    protected $status;

    /**
     * 进程退出码
     * @var int
     */
    protected $exitCode;

    /**
     * 退出信息
     * @var string
     */
    protected $errorMessage;

    /**
     * 是否因信号而结束
     * @var boolean
     */
    protected $ifSignaled;

    /**
     * 导致进程因信号终止的信号代码
     * @var int
     */
    protected $termSignal;

    /**
     * 是否停止
     * @var bool
     */
    protected $ifStopped;

    /**
     * 获取导致进程停止的信号代码
     * @var int
     */
    protected $stopSignal;

    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException("Process expects a callable callback");
        }
        if (!function_exists('pcntl_fork')) {
            throw new RuntimeException("Process need ext-pcntl");
        }
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->isRunning()) {
            throw new RuntimeException("The process is already running");
        }
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new RuntimeException("Could not fork");
        } elseif ($pid) {  //父进程标准子进程号
            $this->pid = $pid;
            $this->status = static::STATUS_RUNNING;
        } else {
            $this->pid = posix_getpid();
            $this->installSignalHandlers();
            try {
                $exitCode = call_user_func($this->callback);
            } catch (\Exception $e) {
                $exitCode  = 1;
            }
            exit(intval($exitCode));
        }
    }


    /**
     * {@inheritdoc}
     */
    public function wait()
    {
        while (true) {
            if ($this->isRunning()) {
                usleep(1000);
            }
        }
    }

    /**
     * 安装信号处理器
     * @return void
     */
    protected function installSignalHandlers()
    {
        foreach ($this->signalHandlers as $signal => $signalHandler) {
            pcntl_signal($signal, $signalHandler);
        }
        //当发送终止信号时，退出当前进程
        pcntl_signal(SIGTERM, function(){
            exit(0);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * {@inheritdoc}
     */
    public function signal($signal)
    {
        posix_kill($this->getPid(), $signal);
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning()
    {
        if ($this->status == static::STATUS_RUNNING) {
            return true;
        }

        return false;
    }

    /**
     * update process status
     * @param bool $blocking
     * @return bool
     */
    protected function updateStatus($blocking = false)
    {
        if ($this->status != static::STATUS_RUNNING) {
            return false;
        }
        $options = $blocking ? 0 : WNOHANG | WUNTRACED;
        $result = pcntl_waitpid($this->getPid(), $status, $options);
        if ($result == -1) {
            throw new RuntimeException("Error waits on or returns the status of the process");
        } elseif ($result) {
            //退出
            $this->status = static::STATUS_TERMINATED;
            //检查状态是否正常退出
            if (pcntl_wifexited($status)) {
                $this->exitCode = pcntl_wexitstatus($status);
                $this->errorMessage = pcntl_strerror($this->exitCode);
            }
            if (pcntl_wifsignaled($status)) {
                $this->ifSignaled = true;
                $this->termSignal = pcntl_wtermsig($status);
            }
            if (pcntl_wifstopped($status)) {
                $this->ifStopped = true;
                $this->stopSignal = pcntl_wifstopped($status);
            }
        } else {
            $this->status = static::STATUS_RUNNING;
        }
    }

    /**
     * 设置信号处理器
     * @param int $signal
     * @param callable $handler
     */
    public function setSignalHandler($signal, $handler)
    {
        if (!is_callable($handler)) {
            throw new InvalidArgumentException('The signal handler should be callable');
        }
        $this->signalHandlers[$signal] = $handler;
    }
}