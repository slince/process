<?php
include __DIR__ . '/vendor/autoload.php';

//use Slince\Process\Pipe\Fifo;
//
//$pathname = sys_get_temp_dir() . '/test.pipe';
//
//$fifo = new Fifo($pathname);
//$message = $fifo->read(true);
//var_dump($message);

use Slince\Process\Process;

//
//$process = new Process(function(){
//    sleep(2);
//    echo 'child exe', PHP_EOL;
//});
//$process->start();
//$process->signal(SIGTERM);
//$process->wait();
////$process->stop();
////$process->wait();
//var_dump($process->isSignaled());
//var_dump($process->getTermSignal());
//var_dump($process->isStopped());
//var_dump($process->getStopSignal());
//exit;
//$exitCode = $process->getExitCode();
//
//var_dump($exitCode);




//use Slince\Process\Pipe\Fifo;
//
//$pathname = sys_get_temp_dir() . '/test.pipe';
//
//$fifo = new Fifo($pathname);
//$message = $fifo->read(true);
//var_dump($message);
//
//use Jenner\SimpleFork\Process;
//
//
//$process = new Process(function(){
//    sleep(2);
//    echo 'child exe', PHP_EOL;
//});
//$process->start();
//$process->shutdown(SIGTERM);
//$process->wait();
////$process->stop();
////$process->wait();
//var_dump($process->ifSignal());
//exit;
//$exitCode = $process->getExitCode();
//
//var_dump($exitCode);


//echo "安装信号处理器...\n";
//pcntl_signal(SIGTERM,  function($signo) {
//    echo "信号处理器被调用\n";
//});
//
//echo "为自己生成SIGHUP信号...\n";
//posix_kill(posix_getpid(), SIGTERM);
//
//echo "分发...\n";
//pcntl_signal_dispatch();
//
//echo "完成\n";


function sig_handler($signo)
{
    $pid = getmypid();
    switch ($signo) {
        case SIGTERM:
            echo $pid, 'term信号触发', PHP_EOL;
            break;
        case SIGHUP:
            echo $pid, 'SIGHUP触发', PHP_EOL;
            break;
        case SIGUSR1:
            echo $pid, 'USER!触发', PHP_EOL;
            break;
        default:
            // 处理所有其他信号
    }

}

pcntl_async_signals(true);

//declare(ticks=1);
pcntl_signal(SIGHUP, 'sig_handler');
pcntl_signal(SIGTERM, 'sig_handler');
pcntl_signal(SIGUSR1, 'sig_handler');
pcntl_signal(SIGUSR2, SIG_IGN);

$pid = pcntl_fork();




if ($pid > 0) {

//    posix_kill($pid, SIGUSR2);

    while(true) {
//        $res = posix_kill($pid, SIGTERM);
//        $res = posix_kill($pid, SIGHUP);
        $res = posix_kill($pid, SIGUSR2);
        sleep(2);
    }

    pcntl_wait($status);
    var_dump($pid);

    if (pcntl_wifexited($status)) {
        echo 'nornal exit', PHP_EOL;
        $exitCode = pcntl_wexitstatus($status);
        var_dump($exitCode);
    }
    if (pcntl_wifsignaled($status)) {
        echo 'signal', PHP_EOL;
        $signal = pcntl_wtermsig($status);
        var_dump($signal);
    }
    if (pcntl_wifstopped($status)) {
        echo 'stoped', PHP_EOL;
        $signal = pcntl_wstopsig($status);
        var_dump($signal);
    }
} elseif ($pid == 0) {
//    pcntl_signal(SIGTERM, 'sig_handler');
    pcntl_signal(SIGUSR2, function(){
        echo 'jixujixujixu',PHP_EOL;
    });
//    pcntl_signal(SIGHUP, 'sig_handler');
    while(true) {
//        pcntl_signal_dispatch();
        sleep(1);
    }
    echo '子进程执行完毕', PHP_EOL;
    exit;
} else {
    exit('fork error');
}