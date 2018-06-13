<?php

namespace deepziyu\swoft\yii\Bootstrap\Listener;

use Swoft\Bean\Annotation\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Http\Server\Event\HttpServerEvent;

/**
 *
 * @Listener(HttpServerEvent::AFTER_REQUEST)
 */
class AfterRequest implements EventHandlerInterface
{
    /**
     * @\Swoft\Bean\Annotation\Inject()
     * @var \deepziyu\swoft\yii\YiiWeb
     */
    public $instance = null;

    public function handle(EventInterface $event)
    {
        HttpServerEvent::AFTER_REQUEST;
        $this->instance->afterRequest();
    }
}