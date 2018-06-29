# swoft-yii2

完美滴在 swoft 框架中使用 yii2 的组件。

_此插件依赖于 [yii2-swoole](https://github.com/deepziyu/yii2-swoole) 实现_

## 已支持的组件

- yii2-db 易框架的 MySQL-Connect 、 ActiveRecord 等

- yii2-log 易框架的日志组件，推荐还是用 swoft-log 吧

- yii2-cache 易框架的缓存组件，

## 安装

#### 环境要求

- swoft-v1.0 以上

#### composer install 插件安装

- 在项目中的 `composer.json` 文件中添加依赖：

```json
{
  "require-dev": {
      "deepziyu/swoft-yii2": "dev-master"
  }
}
```

- 次插件依赖 Yii2-framework ，所以 composer 安装 Yii2 时会出现一些问题，具体解决方法参考 [Yii2-installation](https://www.yiiframework.com/doc/guide/2.0/zh-cn/start-installationinstallation)。
- 执行 `$ php composer.phar update` 或 `$ composer update` 进行安装。

## 配置

在 swoft 项目的 config/app.php 中添加如下配置：

```php
\Swoft\App::setAliases([
    // you can wirte it in `config/define.php` also
    '@swoft-yii2' => '@vendor/deepziyu/swoft-yii2/src'
]);
return [
    'bootScan'     => [
        // ···· other beans
        'deepziyu\swoft\yii' => \Swoft\App::getAlias('@swoft-yii2'),
    ],
    'beanScan'     => [
        // ···· other beans
        'deepziyu\swoft\yii' => \Swoft\App::getAlias('@swoft-yii2'),
    ],
    'yiiConfig' => require __DIR__ . DS . 'yii.php',
];
```
yii.php 文件配置就是常规的 Yii-Config 了:

```php
return [
    'env' => 'dev', // YII_ENV 的值 VALUE
    'debug' => true,// YII_DEBUG 的值 VALUE
    'config' => [   // 常规的 Yii-Config
        'id' => 'swotf-test',
        'basePath' => BASE_PATH,
        'language' => 'zh-CN',
        'timeZone' => 'Asia/Shanghai',
        'bootstrap' => ['log'],
        'components' => [
            'request' => [
                'cookieValidationKey' => 'php is the best!!',
            ],
            'log' => [
                'traceLevel' => 3,
                'targets' => [
                    [
                        'class' => 'yii\log\FileTarget',
                        'levels' => ['error', 'warning'],
                    ],
                ],
            ],
            'db' => [
                'class' => 'deepziyu\yii\swoole\db\Connection',
                'dsn' => 'mysql:host=127.0.0.1;dbname=testDb',
                'username' => 'test',
                'password' => 'testP',
                'charset' => 'utf8',
                'enableReloadSchema' => true, // must be true
                'attributes' => [
                    //MysqlPoolPdo::POOL_MAX_SIZE => 50, 连接池的大小
                    //MysqlPoolPdo::POOL_MAX_SLEEP_Times => 0.1, 连接池满栈时等待的秒
                ],
                'slaves' => [
                    // 支持主从配置
                    // support Yii2 salves config 
                ]
            ],
            'cache' => [
                'class' => 'yii\caching\ArrayCache',
            ],
        ]
    ],
];
```

## 愉快的使用咯

```php 
    /**
     * 测试 AR 查询
     * @RequestMapping("/yii/ar")
     */
    public function yiiAR()
    {
        return [
            'query1' => \Yii::$app->getDb()->createCommand("select 1")->queryAll(),
            'query2' => (new Query())->from('tag')->where(['tagid' => 1])->one(),
            'query3' => (new class extends ActiveRecord
            {
                public static function tableName()
                {
                    return 'tag2';
                }

                public function rules()
                {
                    return [[['tagname', 'tagid'], 'safe']];
                }
            })->find()->asArray()->all(),

        ];
    }
```

## more words 再多说几句

- 此插件会单独维护一份协程的上下文，和数据库连接池，所以会吃点性能咯

- 没有支持的组件最好不要用，嘿，容易出问题滴

- 我们不支持各种 defer 特性，这麒麟臂不好控制

- 此插件目前仅在 Swoole\Http\Server 模式下加载 

- 在 Yii2-framework 会自己记录一下日志，比如 `Yii::error('hello')` 此类的，这些日志会记录在 SWOFT_BASE_PATH\runtime\logs\app.log 里


## Chat && Help

Swoft 框架 QQ 群: 548173319 (@小紫羽)