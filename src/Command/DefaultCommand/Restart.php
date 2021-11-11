<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-24
 * Time: 23:57
 */

namespace EasySwoole\EasySwoole\Command\DefaultCommand;

use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\EasySwoole\Command\Utility;

class Restart implements CommandInterface
{

    public function commandName(): string
    {
        return 'restart';
    }

    public function exec(array $args): ?string
    {
        $force = false;
        if(in_array('force',$args)){
            $force = true;
        }
        $Conf = Config::getInstance();
        $pidFile = $Conf->getConf("MAIN_SERVER.SETTING.pid_file");
        if (file_exists($pidFile)) {
            $pid = intval(file_get_contents($pidFile));
            if (\Swoole\Process::kill($pid,0)){
                if ($force) {
                    \Swoole\Process::kill($pid, SIGKILL);
                } else {
                    \Swoole\Process::kill($pid, SIGTERM);
                }
            }
            return '';
        } else {
            return "PID file does not exist, please check whether to run in the daemon mode!";
        }
    }

    public function help(array $args): ?string
    {
        $logo = Utility::easySwooleLog();
        return $logo.<<<HELP_START
\e[33mOperation:\e[0m
\e[31m  php rayswoole restart [arg1] [arg2]\e[0m
\e[33mIntro:\e[0m
\e[36m  to restart current rayswoole server \e[0m
\e[33mArg:\e[0m
\e[32m  force \e[0m                   force to kill server
HELP_START;
    }
}
