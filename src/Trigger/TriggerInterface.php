<?php


namespace EasySwoole\EasySwoole\Trigger;


interface TriggerInterface
{
    public function error($msg, int $errorCode = E_USER_ERROR, $file = null, $line = null);
    public function throwable(\Throwable $throwable);
}