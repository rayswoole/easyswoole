<?php


namespace EasySwoole\EasySwoole\Task\AbstractInterface;


interface TaskInterface
{
    function onException(\Throwable $throwable,int $taskId,int $workerIndex);
}