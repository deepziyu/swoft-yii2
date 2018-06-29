<?php
namespace deepziyu\swoft\yii;

use deepziyu\yii\swoole\coroutine\Context;
use deepziyu\yii\swoole\di\Container;
use deepziyu\yii\swoole\di\NullContainer;
use deepziyu\yii\swoole\web\Application;
use deepziyu\yii\swoole\web\Request;
use Swoft\App;
use Swoft\Core\RequestContext;
use Swoole\Server;
use Swoole\Http\Server as HttpServer;
use Swoole\Http\Request as SwooleRequest;

/**
 * Class YiiWeb
 * @Swoft\Bean\Annotation\Bean()
 * @package deepziyu\yii\swoft
 */
class YiiWeb
{
    public $config = [];

    public  $debug = true;

    public  $env = 'prod';

    private $enable = true;

    public function __construct()
    {

    }

    public function onWorkerStart(Server $server, int $workerId, bool $isWorker)
    {
        if(!$isWorker){
            return $this->enable = false;
        }

        if(!$server instanceof HttpServer){
            return $this->enable = false;
        }

        $config = App::getProperties();
        if(!empty($config['yiiConfig'])){
            $yiiConfig = $config['yiiConfig'];
            $this->config = $yiiConfig['config'];
            $this->debug = $yiiConfig['debug'];
            $this->env = $yiiConfig['env'];
        }else{
            return $this->enable = false;
        }

        defined('YII_DEBUG') or define('YII_DEBUG', $this->debug);
        defined('YII_ENV') or define('YII_ENV', $this->env);
        defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER',false); //必须关闭 Yii 的错误处理器

        $path = App::getAlias('@vendor/deepziyu/yii2-swoole/Yii.php');
        require_once($path);
        \Yii::$container = new NullContainer();
        \Yii::$context = new Context();
        return true;
    }

    public function onWorkerStop(Server $server, int $workerId)
    {
        //TODO Swoft is not supported this Event-Type;
    }

    public function beforeRequest()
    {
        if(!$this->enable){
            return false;
        }

        \Yii::$context->setContextDataByKey(Context::COROUTINE_CONTAINER, new Container());
        \Yii::$context->setContextDataByKey(Context::COROUTINE_APP, $this->getYiiApplication());

        /** @var Request $yiiRequest */
        $yiiRequest = \Yii::$app->getRequest();
        $swooleRequest = RequestContext::getRequest()->getSwooleRequest();
        $yiiRequest->setSwooleRequest($swooleRequest);

        //\Swoole\Http\Response::end(); 后会释放Http请求的 buffer ，此后不能再使用 \Swoole\Http\Request::getRawBody() 了
        $yiiRequest->getRawBody();
        $yiiRequest->getCookies();

        return true;
    }

    public function afterRequest()
    {
        if(!$this->enable){
            return false;
        }

        //\Yii::getLogger()->flush();
        \Yii::getLogger()->flush(true);
        \Yii::$context->destory();
    }

    public function getYiiApplication()
    {
        return new Application($this->config);
    }
}