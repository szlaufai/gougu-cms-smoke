<?php
 
// [ 应用入口文件 ]
namespace think;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers:*');
header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS');

if (empty(file_exists(__DIR__ . '/../vendor/autoload.php'))) {
    echo '您还未安装PHP依赖包，请输入命令安装：composer install，安装教程点击<a href="https://blog.gougucms.com/home/book/detail/bid/1/id/2.html" target="_blank">这里</a>。';
    exit;
}
require __DIR__ . '/../vendor/autoload.php';

// 定义当前版本号
define('CMS_VERSION','4.0.825');

// 定义Layui版本号
define('LAYUI_VERSION','2.7.6');

// 定义项目目录
define('CMS_ROOT', __DIR__ . '/../');

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);
