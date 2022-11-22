<?php

declare (strict_types = 1);

namespace app\api;

use think\App;
use think\exception\HttpResponseException;
use think\facade\Request;
use think\Response;
use Firebase\JWT\JWT;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 分页数量
     * @var string
     */
    protected $pageSize = '';

    /**
     * jwt配置
     * @var string
     */
    protected $jwt_conf = [
        'secrect' => 'gougucms',
        'iss' => 'www.gougucms.com', //签发者 可选
        'aud' => 'gougucms', //接收该JWT的一方，可选
        'exptime' => 7200, //过期时间,这里设置2个小时
    ];
    /**
     * 构造方法
     * @access public
     * @param  App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
		$this->jwt_conf = get_system_config('token');
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        //每页显示数据量
        $this->pageSize = Request::param('page_size', \think\facade\Config::get('app.page_size'));
    }

    /**
     * Api处理成功结果返回方法
     * @param      $message
     * @param null $redirect
     * @param null $extra
     * @return mixed
     * @throws ReturnException
     */
    protected function apiSuccess($data=[],$msg = 'success')
    {
		return $this->apiReturn($data, 0, $msg);
    }

    /**
     * Api处理结果失败返回方法
     * @param      $error_code
     * @param      $message
     * @param null $redirect
     * @param null $extra
     * @return mixed
     * @throws ReturnException
     */
    protected function apiError($msg = 'fail',$data=[], $code = 1)
    {
        return $this->apiReturn($data, $code, $msg);
    }

    /**
     * 返回封装后的API数据到客户端
     * @param  mixed   $data 要返回的数据
     * @param  integer $code 返回的code
     * @param  mixed   $msg 提示信息
     * @param  string  $type 返回数据格式
     * @param  array   $header 发送的Header信息
     * @return Response
     */
    protected function apiReturn($data, int $code = 0, $msg = '', string $type = '', array $header = []): Response
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => time(),
            'data' => $data,
        ];

        $type = $type ?: 'json';
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * @param $user_id
     * @return string
     */
    protected function getToken($user_id){
        $time = time(); //当前时间
        $conf = $this->jwt_conf;
        $token = [
            'iss' => $conf['iss'], //签发者 可选
            'aud' => $conf['aud'], //接收该JWT的一方，可选
            'iat' => $time, //签发时间
            'nbf' => $time-1 , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time+$conf['exptime'], //过期时间,这里设置2个小时
            'data' => [
                //自定义信息，不要定义敏感信息
                'userid' =>$user_id,
            ]
        ];
        return JWT::encode($token, $conf['secrect'], 'HS256'); //输出Token  默认'HS256'
    }

    /**
     * @param $email
     * @return string
     */
    protected function getEmailToken($email){
        $time = time(); //当前时间
        $conf = $this->jwt_conf;
        $token = [
            'iss' => $conf['iss'], //签发者 可选
            'aud' => $conf['aud'], //接收该JWT的一方，可选
            'iat' => $time, //签发时间
            'nbf' => $time-1 , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time+$conf['exptime'], //过期时间,这里设置2个小时
            'data' => [
                //自定义信息，不要定义敏感信息
                'email' => $email
            ]
        ];
        return JWT::encode($token, $conf['secrect'], 'HS256'); //输出Token  默认'HS256'
    }
}
