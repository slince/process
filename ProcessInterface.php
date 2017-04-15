<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process;

interface ProcessInterface
{
    /**
     * 获取进程号
     * @return resource
     */
    public function getPid();

    /**
     * 开始执行进程
     */
    public function start();

    /**
     * 等待进程执行
     */
    public function wait();

    /**
     * 执行进程
     * @return void
     */
    public function run();

    /**
     * 给进程发信号
     * @param int $signal pcntl sinal
     * @return boolean
     */
    public function signal($signal);

    /**
     * 是否正在执行
     * @return bool
     */
    public function isRunning();
}