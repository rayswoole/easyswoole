<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-24
 * Time: 23:44
 */

namespace EasySwoole\EasySwoole\Command\DefaultCommand;


use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\SysConst;
use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\EasySwoole\Command\Utility;
use rayswoole\Core;

class Run implements CommandInterface
{

    public function commandName(): string
    {
        return 'run';
    }

    public function exec(array $args): ?string
    {
        Utility::opCacheClear();
        $response = Utility::easySwooleLog();
        $conf = Config::getInstance();
        //create main Server
        Core::getInstance()->globalInitialize()->createServer();
        $serverType = $conf->getConf('MAIN_SERVER.SERVER_TYPE');
        switch ($serverType) {
            case EASYSWOOLE_SERVER:
                {
                    $serverType = 'SWOOLE_SERVER';
                    break;
                }
            case EASYSWOOLE_WEB_SERVER:
                {
                    $serverType = 'SWOOLE_WEB';
                    break;
                }
            case EASYSWOOLE_WEB_SOCKET_SERVER:
                {
                    $serverType = 'SWOOLE_WEB_SOCKET';
                    break;
                }
            case EASYSWOOLE_REDIS_SERVER:
                {
                    $serverType = 'SWOOLE_REDIS';
                    break;
                }
            default:
                {
                    $serverType = 'UNKNOWN';
                }
        }
        $response .= Utility::displayItem('main server', $serverType) . "\n";
        $response .= Utility::displayItem('listen address', $conf->getConf('MAIN_SERVER.LISTEN_ADDRESS')) . "\n";
        $response .= Utility::displayItem('listen port', $conf->getConf('MAIN_SERVER.PORT')) . "\n";
        $list = ServerManager::getInstance()->getSubServerRegister();
        $index = 1;
        foreach ($list as $serverName => $item) {
            if (empty($item['setting'])) {
                $type = $serverType;
            } else {
                $type = $item['type'] % 2 > 0 ? 'SWOOLE_TCP' : 'SWOOLE_UDP';
            }
            $response .= Utility::displayItem("sub server:{$serverName}", "{$type}@{$item['listenAddress']}:{$item['port']}") . "\n";
            $index++;
        }
        $ipcount = 0;
        $ips = swoole_get_local_ip();
        foreach ($ips as $eth => $val) {
            $response .= Utility::displayItem('ip@' . $eth, $val) . "\n";
            $ipcount++;
            if ($ipcount >= 5) {
                $response .= Utility::displayItem('ip@' . $eth, 'too many ips, hide the remaining') . "\n";
                break;
            }
        }

        $data = $conf->getConf('MAIN_SERVER.SETTING');
        if(empty($data['user'])){
            $data['user'] = get_current_user();
        }

        foreach ($data as $key => $datum){
            $response .= Utility::displayItem($key,$datum) . "\n";
        }

        $response .= Utility::displayItem('php version', phpversion()) . "\n";
        $response .= Utility::displayItem('swoole version', phpversion('swoole')) . "\n";
        $response .= Utility::displayItem('zip version', phpversion('zip')) . "\n";
        //$response .= Utility::displayItem('easy swoole', SysConst::EASYSWOOLE_VERSION) . "\n";
        $response .= Utility::displayItem('temp dir', RAY_TEMP_DIR) . "\n";
        $response .= Utility::displayItem('log dir', RAY_LOG_DIR) . "\n";
        echo $response;
        Core::getInstance()->start();
        return null;
    }

    public function help(array $args): ?string
    {
        $logo = Utility::easySwooleLog();
        return $logo . <<<HELP_START
\e[33mOperation:\e[0m
\e[31m  php rayswoole run [arg1] \e[0m
\e[33mIntro:\e[0m
\e[36m  to start current rayswoole server \e[0m
\e[33mArg:\e[0m
\e[32m  dev \e[0m                     start server in debug mode
HELP_START;
    }
}