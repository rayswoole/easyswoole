<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-24
 * Time: 23:15
 */

namespace EasySwoole\EasySwoole\Command\DefaultCommand;


use EasySwoole\EasySwoole\Command\CommandContainer;
use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\EasySwoole\Command\Utility;

class Help implements CommandInterface
{

    public function commandName(): string
    {
        return 'help';
    }

    public function exec(array $args): ?string
    {
        if (!isset($args[0])) {
            return $this->help($args);
        } else {
            $actionName = $args[0];
            array_shift($args);
            $call = CommandContainer::getInstance()->get($actionName);
            if ($call instanceof CommandInterface) {
                return $call->help($args);
            } else {
                return "no help message for command {$actionName} was found";
            }
        }
    }

    public function help(array $args): ?string
    {
        $allCommand = implode(PHP_EOL, CommandContainer::getInstance()->getCommandList());
        $logo = Utility::easySwooleLog();
        return $logo.<<<HELP
Welcome To RAYSWOOLE Command Console!
Usage: php rayswoole [command] [arg]
Get help : php rayswoole help [command]
Current Register Command:
{$allCommand}
HELP;
    }
}