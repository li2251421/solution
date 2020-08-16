<?php

/**
 * 信号驱动IO，kill -s SIGUSR1 pid 或 posix_kill(pid, SIGUSR1);
 */

require "../../vendor/autoload.php";

// 信号安装处理器
dd("Install signal handler");
$signos = [SIGHUP, SIGINT, SIGQUIT, SIGTERM, SIGCONT, SIGIO, SIGUSR1, SIGUSR2];

foreach ($signos as $signo) {
    pcntl_signal($signo, "sig_handler");
}

// 发送信号
//posix_kill(posix_getpid(), SIGIO);

while (true) {
    // 调用信号处理器
    pcntl_signal_dispatch();
}

// 信号处理函数
function sig_handler($signo)
{
    switch ($signo) {
        case SIGHUP:
            // 终端断线，重新读取配置文件等
            dd('SIGHUP: ' . $signo, "sig_handler");
            break;
        case SIGINT:
            // 中断（同 Ctrl + C）
            dd('SIGINT: ' . $signo, "sig_handler");
            break;
        case SIGQUIT:
            // 退出（同 Ctrl + \）
            dd('SIGQUIT: ' . $signo, "sig_handler");
            break;
        case SIGTERM:
            // 终止
            dd('SIGTERM: ' . $signo, "sig_handler");
            break;
//        case SIGKILL:
//            // 强制中止
//            dd('SIGKILL: ' . $signo, "sig_handler");
//            break;
        case SIGCONT:
            // 继续（与STOP相反， fg/bg命令）
            dd('SIGCONT: ' . $signo, "sig_handler");
            break;
//        case SIGSTOP:
//            // 暂停（同 Ctrl + Z）
//            dd('SIGSTOP: ' . $signo, "sig_handler");
//            break;
        case SIGIO:
            // 文件描述符准备就绪
            dd('SIGIO: ' . $signo, "sig_handler");
            break;
        case SIGUSR1:
            // 留给用户使用
            dd('SIGUSR1: ' . $signo, "sig_handler");
            break;
        case SIGUSR2:
            // 留给用户使用
            dd('SIGTERM: ' . $signo, "sig_handler");
            break;
        default:
            dd($signo, 'not install signal');
            break;
    }
}
