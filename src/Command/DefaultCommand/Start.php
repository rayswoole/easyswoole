<?php
/**
 ** RAYSWOOLE [ HIGH PERFORMANCE CMS BASED ON SWOOLE ]
 ** ----------------------------------------------------------------------
 ** Copyright © 2020 http://haoguangyun.com All rights reserved.
 ** ----------------------------------------------------------------------
 ** Author: haoguangyun <admin@haoguangyun.com>
 ** ----------------------------------------------------------------------
 ** Last-Modified: 2021-01-01 08:00
 ** ----------------------------------------------------------------------
 **/

namespace EasySwoole\EasySwoole\Command\DefaultCommand;

use EasySwoole\EasySwoole\Config;
use Swoole\Process;
use EasySwoole\EasySwoole\Command\CommandContainer;
use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\EasySwoole\Command\Utility;

class Start implements CommandInterface
{
    public $pid = 0;
    private $define_dev = false;
    private $daemon_file = '';

    public function commandName(): string
    {
        return 'start';
    }

    public function exec(array $args): ?string
    {
        if (!extension_loaded('swoole')){
            return "\033[41;37m" . "!ERROR : Module swoole is not loaded \033[0m\n";
        }
        $serverName = Config::getInstance()->getConf('SERVER_NAME');
        if(!in_array(PHP_OS,['Darwin','CYGWIN','WINNT'])){
            cli_set_process_title($serverName);
        }
        if ($this->exists()){
            if (in_array('force',$args)){
                $this->stop();
            } else {
                return "\033[41;37m" . "!ERROR : Service has started, try : ps -aux | grep ".$serverName." \033[0m\n";
            }
        }
        if (in_array('dev',$args)){
            $this->define_dev = true;
        }

        $this->daemon_file = Config::getInstance()->getConf('MAIN_SERVER.daemon_file');
        file_put_contents($this->daemon_file, getmypid());

        $this->start();

        return '';
    }

    public function start()
    {
        $php_bin = Config::getInstance()->getConf("MAIN_SERVER.PHP_PATH");
        if ($php_bin === ''){
            if (function_exists('shell_exec')) {
                $php_bin = trim(shell_exec('which php'));
            } elseif (file_exists('/usr/bin/php')) {
                $php_bin = '/usr/bin/php';
            } elseif (function_exists('phpinfo')) {
                ob_start();
                ob_implicit_flush();
                phpinfo(1);
                $content = ob_get_clean();
                if (preg_match('/\'\-\-prefix=(.*?)\'/', $content, $match)) {
                    if (isset($match[1])) {
                        $php_bin = $match[1].'/bin/php';
                    }
                }
            }
        }
        if (!is_file($php_bin)){
            echo "\033[41;37m" . "!ERROR : Please open the Config/App.php file and configure the MAIN_SERVER.PHP_PATH node \033[0m\n";
            echo "\033[41;37m" . "!ERROR : Or enable function shell_exec()  \033[0m\n";
            exit();
        }
        if (function_exists('readlink') && is_link($php_bin)){
            $php_bin = readlink($php_bin);
        }

        $pool = new \Swoole\Process\Pool(1);
        $pool->on("WorkerStart", function ($pool, $workerId) use ($php_bin) {
            $worker = $pool->getProcess();
            $cmd = [RAY_ROOT.'/rayswoole','run'];
            if ($this->define_dev){
                $cmd[] = 'dev';
            }
            $worker->exec($php_bin, $cmd);
        });
        echo "Server start at " . date("Y-m-d H:i:s") . "\n";
        echo "Parent Process ID: ".getmypid()."\n";
        $pool->start();
    }

    private function wait()
    {
        while (Process::wait(false));
    }

    public function stop()
    {
        $pid = $this->pid;
        if ($pid > 1000 && Process::kill($pid, 0)){
            Process::kill($this->pid);
            $this->wait();
        }

        $pidFile = Config::getInstance()->getConf("MAIN_SERVER.SETTING.pid_file");
        if (file_exists($pidFile)) {
            $pid = intval(file_get_contents($pidFile));
            if (Process::kill($pid, 0)) {
                Process::kill($pid);
                $this->wait();
            }
        }
        $time = time();
        while (true) {
            if (Process::kill($pid, 0)) {
                $this->wait();
                usleep(200);
            } else {
                if (file_exists($pidFile)) {
                    unlink($pidFile);
                }
                break;
            }
            if (time() - $time > 5) {
                echo "\033[41;37m" . "!stop server fail , try : kill -9 " .$pid. "   \033[0m\n";
                break;
            }
        }
        $this->pid = 0;
    }

    private function exists()
    {
        $pid = $this->pid;
        if ($pid > 1000 && Process::kill($pid, 0)){
            return true;
        }
        $pidFile = Config::getInstance()->getConf("MAIN_SERVER.SETTING.pid_file");
        if (is_file($pidFile)) {
            $pid = intval(file_get_contents($pidFile));
            if (Process::kill($pid, 0)) {
                return true;
            }
        }
        return false;
    }

    public function help(array $args): ?string
    {
        $allCommand = implode(PHP_EOL, CommandContainer::getInstance()->getCommandList());
        $logo = Utility::easySwooleLog();
        return $logo.<<<HELP
\e[33mOperation:\e[0m
\e[31m  php rayswoole start [arg1] [arg2]  \e[0m
\e[33mIntro:\e[0m
\e[36m  to start current rayswoole server \e[0m
\e[33mArg:\e[0m
\e[32m  dev \e[0m                     start server in debug mode
\e[32m  force \e[0m                   service will be forced to start
HELP;
    }
}