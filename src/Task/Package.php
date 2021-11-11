<?php


namespace EasySwoole\EasySwoole\Task;


class Package
{
    const ASYNC = 1;
    const SYNC = 2;
    protected $type;
    protected $task;
    protected $onFinish;
    protected $method = '';
    protected $data;

    /**
     * @var float
     */
    protected $expire;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param mixed $task
     */
    public function setTask($task): void
    {
        $this->task = $task;
    }

    /**
     * @return mixed
     */
    public function getOnFinish()
    {
        return $this->onFinish;
    }

    /**
     * @param mixed $onFinish
     */
    public function setOnFinish($onFinish): void
    {
        $this->onFinish = $onFinish;
    }

    /**
     * @return float
     */
    public function getExpire(): float
    {
        return $this->expire;
    }

    /**
     * @param float $expire
     */
    public function setExpire(float $expire): void
    {
        $this->expire = $expire;
    }

    /**
     * @return string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }
}