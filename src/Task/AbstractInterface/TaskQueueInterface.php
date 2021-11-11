<?php


namespace EasySwoole\EasySwoole\Task\AbstractInterface;


use EasySwoole\EasySwoole\Task\Package;

interface TaskQueueInterface
{
    function pop():?Package;
    function push(Package $package):bool ;
}