<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-25
 * Time: 11:12
 */

namespace EasySwoole\EasySwoole\Command\DefaultCommand;


use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\EasySwoole\Command\Utility;
use rayswoole\Core;

class Reload implements CommandInterface
{

    public function commandName(): string
    {
        return 'reload';
    }

    public function exec(array $args): ?string
    {
        $conf = Config::getInstance();
        $res = '';
        $pidFile = $conf->getConf("MAIN_SERVER.SETTING.pid_file");
        if (file_exists($pidFile)) {
            $sig = SIGUSR1;
            $res = $res . Utility::displayItem('reloadType', "all-worker") . "\n";
            Utility::opCacheClear();
            $pid = file_get_contents($pidFile);
            if (!\swoole_process::kill($pid, 0)) {
                return "pid :{$pid} not exist ";
            }
            \swoole_process::kill($pid, $sig);
            return $res . "send server reload command at " . date("Y-m-d H:i:s");
        } else {
            return "PID file does not exist, please check whether to run in the daemon mode!";
        }
    }

    public function help(array $args): ?string
    {
        $logo = Utility::easySwooleLog();
        return $logo . <<<HELP_RELOAD
\e[33mOperation:\e[0m
\e[31m  php rayswoole reload [arg1]\e[0m
\e[33mIntro:\e[0m
\e[36m  you can reload current rayswoole server\e[0m
\e[33mAgs:\e[0m
\e[32m  produce \e[0m                     load produce.php
HELP_RELOAD;
    }
}
