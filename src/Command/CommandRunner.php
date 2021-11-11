<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-24
 * Time: 23:11
 */

namespace EasySwoole\EasySwoole\Command;


use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Command\DefaultCommand\Crontab;
use EasySwoole\EasySwoole\Command\DefaultCommand\Help;
use EasySwoole\EasySwoole\Command\DefaultCommand\PhpUnit;
use EasySwoole\EasySwoole\Command\DefaultCommand\Process;
use EasySwoole\EasySwoole\Command\DefaultCommand\Reload;
use EasySwoole\EasySwoole\Command\DefaultCommand\Restart;
use EasySwoole\EasySwoole\Command\DefaultCommand\Run;
use EasySwoole\EasySwoole\Command\DefaultCommand\Status;
use EasySwoole\EasySwoole\Command\DefaultCommand\Config;
use EasySwoole\EasySwoole\Command\DefaultCommand\Task;
use EasySwoole\EasySwoole\Command\DefaultCommand\Start;
use rayswoole\Core;

class CommandRunner
{
    use Singleton;

    function __construct()
    {
        CommandContainer::getInstance()->set(new Help());
        CommandContainer::getInstance()->set(new Run());
        CommandContainer::getInstance()->set(new Restart());
        CommandContainer::getInstance()->set(new Reload());
        //CommandContainer::getInstance()->set(new PhpUnit());
        CommandContainer::getInstance()->set(new Config());
        CommandContainer::getInstance()->set(new Process());
        CommandContainer::getInstance()->set(new Status());
        CommandContainer::getInstance()->set(new Task());
        CommandContainer::getInstance()->set(new Crontab());
        CommandContainer::getInstance()->set(new Start());
    }

    function run(array $args):?string
    {
        $command = array_shift($args);
        if(empty($command)){
            $command = 'help';
        }else if($command != 'install'){
            if (in_array('dev',$args)){
                Core::getInstance()->setIsDev(true);
            }
            Core::getInstance()->initialize();
        }
        if(!CommandContainer::getInstance()->get($command)){
            $command = 'help';
        }
        return CommandContainer::getInstance()->hook($command,$args);
    }
}