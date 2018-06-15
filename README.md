# swoft-yii2

完美滴在 swoft 框架中使用 yii2 的组件。

_此插件依赖于[yii2-swoole](https://github.com/deepziyu/yii2-swoole)实现_

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


- 执行 `$ php composer.phar update` 或 `$ composer update` 进行安装。

## 配置

在 swoft 项目的 config/app.php 中添加如下配置：

```php
\Swoft\App::setAliases([
    '@swoft-yii2' => '@vendor/deepziyu/swoft-yii2/src'
]);
return [
    'bootScan'     => [
        'deepziyu\swoft\yii' => \Swoft\App::getAlias('@swoft-yii2'),
    ],
    'beanScan'     => [
        'deepziyu\swoft\yii' => \Swoft\App::getAlias('@swoft-yii2'),
    ],
    'yiiConfig' => require __DIR__ . DS . 'yii.php',
];
```
yii.php 文件配置就是常规的 Yii-Config 了:

```php
return [
    'env' => 'dev', // YII_ENV 的值
    'debug' => true,// YII_DEBUG 的值
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
                'enableReloadSchema' => true,
                'attributes' => [
                    //MysqlPoolPdo::POOL_MAX_SIZE => 50, 连接池的大小
                    //MysqlPoolPdo::POOL_MAX_SLEEP_Times => 0.1, 连接池满栈时等待的秒
                ],
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

- 此插件会单独维护一份协程的上下文，和数据库连接池，so，会吃点性能咯

- 没有支持的组件最好不要用，嘿，容易出问题滴

- 我们不支持各种 defer 特性


## Chat && Help

Swoft 框架 QQ 交流群: 小紫羽@548173319