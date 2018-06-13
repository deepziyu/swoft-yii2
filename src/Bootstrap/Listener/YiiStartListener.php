<?php

namespace deepziyu\swoft\yii\Bootstrap\Listener;

use deepziyu\swoft\yii\YiiWeb;
use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Bootstrap\Listeners\Interfaces\WorkerStartInterface;
use Swoft\Bootstrap\Listeners\Interfaces\WorkerStopInterface;
use Swoft\Bootstrap\SwooleEvent;
use Swoole\Server;
use Swoft\Bean\Annotation\ServerListener;

/**
 *
 * @ServerListener(event={
 *     SwooleEvent::ON_WORKER_START
 * })
 */
class YiiStartListener implements WorkerStartInterface, WorkerStopInterface
{
    /**
     * @\Swoft\Bean\Annotation\Inject()
     * @var YiiWeb
     */
    public $instance = null;

    public function onWorkerStart(Server $server, int $workerId, bool $isWorker)
    {
        SwooleEvent::ON_WORKER_START;
        $this->instance->onWorkerStart($server, $workerId, $isWorker);
    }

    public function onWorkerStop(Server $server, int $workerId)
    {
        SwooleEvent::ON_WORKER_STOP;
        $this->instance->onWorkerStop($server, $workerId);
    }

}