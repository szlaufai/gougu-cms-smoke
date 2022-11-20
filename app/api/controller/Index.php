<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\api\middleware\EmailAuth;
use app\api\validate\IndexCheck;
use app\facade\EmailVerify;
use app\model\RecycleOrder;
use app\model\User;
use think\App;
use think\exception\ValidateException;
use think\facade\Db;


class Index extends BaseController
{
    /**
     * 控制器中间件 [登录、注册 不需要鉴权]
     * @var array
     */
	protected $middleware = [
    	EmailAuth::class => ['only' => ['reg','resetPassword']]
    ];
	
    /**
     * @api {post} /index/index API页面
     * @apiDescription  返回首页信息
     */
    public function index()
    {
        $this->apiSuccess();
    }

    /**
     * @api {post} /index/login 会员登录
     * @apiDescription 系统登录接口，返回 token 用于操作需验证身份的接口

     * @apiParam (请求参数：) {string}             username 登录用户名
     * @apiParam (请求参数：) {string}             password 登录密码

     * @apiParam (响应字段：) {string}             token    Token

     * @apiSuccessExample {json} 成功示例
     * {"code":0,"msg":"登录成功","time":1627374739,"data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcGkuZ291Z3VjbXMuY29tIiwiYXVkIjoiZ291Z3VjbXMiLCJpYXQiOjE2MjczNzQ3MzksImV4cCI6MTYyNzM3ODMzOSwidWlkIjoxfQ.gjYMtCIwKKY7AalFTlwB2ZVWULxiQpsGvrz5I5t2qTs"}}
     * @apiErrorExample {json} 失败示例
     * {"code":1,"msg":"帐号或密码错误","time":1627374820,"data":[]}
     */
    public function login()
    {
		$param = get_params();
        try {
            validate(IndexCheck::class)->scene(request()->action())->check($param);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        // 校验用户名密码
		$user = User::where(['email' => $param['email']])->find();
        if (empty($user)) {
            $this->apiError('帐号不存在');
        }
        $param['pwd'] = set_password($param['password'], $user['salt']);
        if ($param['pwd'] !== $user['password']) {
            $this->apiError('密码错误');
        }
        if ($user['status'] == -1) {
            $this->apiError('该用户禁止登录,请与平台联系');
        }
        $data = [
            'last_login_time' => time(),
            'last_login_ip' => request()->ip(),
            'login_num' => $user['login_num'] + 1,
        ];
        $res = $user->save($data);
        if ($res) {
            $token = self::getToken($user['id']);
			add_user_log('api', '登录');
            $this->apiSuccess(['token' => $token]);
        }
    }

    /**
     * @api {post} /index/reg 会员注册
     * @apiDescription  系统注册接口，返回是否成功的提示，需再次登录

     * @apiParam (请求参数：) {string}             username 用户名
     * @apiParam (请求参数：) {string}             password 密码

     * @apiSuccessExample {json} 成功示例
     * {"code":0,"msg":"注册成功","time":1627375117,"data":[]}
     * @apiErrorExample {json} 失败示例
     * {"code":1,"msg":"该账户已经存在","time":1627374899,"data":[]}
     */
    public function reg()
    {
		$param = get_params();
        try {
            validate(IndexCheck::class)->scene(request()->action())->check($param);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
		$user = User::where(['email' => $param['email']])->find();
        if (!empty($user)) {
			$this->apiError('该账户已经存在');
        }
        $param['salt'] = set_salt(20);
        $param['password'] = set_password($param['password'], $param['salt']);
        $param['register_time'] = time();
        $param['headimgurl'] = '/static/admin/images/icon.png';
        $param['register_ip'] = request()->ip();
        $param['approval_status'] = $param['type'] == 2 ? 0 : 1;
        $uid = User::strict(false)->field(true)->insertGetId($param);
		if($uid){
			add_user_log('api', '注册');
            $token = self::getToken($uid);
            $this->apiSuccess(['token' => $token]);
		}else{
			$this->apiError('注册失败,请重试');
		}
    }

    /**
     * 重置密码
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function resetPassword()
    {
        $param = get_params();
        try {
            validate(IndexCheck::class)->scene(request()->action())->check($param);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        $user = User::where(['email' => $param['email']])->find();
        if (empty($user)) {
            $this->apiError('该账户不存在');
        }
        $param['salt'] = set_salt(20);
        $param['password'] = set_password($param['password'], $param['salt']);
        $uid = User::strict(false)->where('id',$user['id'])->field(['password','salt'])->update($param);
        if($uid){
            add_user_log('api', '重置密码');
            $this->apiSuccess();
        }else{
            $this->apiError('重置密码失败,请重试');
        }
    }

    /**
     * 发送邮件验证码
     */
    public function sendVerifyCode(){
        $params = get_params();
        try {
            validate(IndexCheck::class)->scene(request()->action())->check($params);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        // 设置缓存数据
        $cacheName = 'email_limit'.base64_encode($params['email']);
        if (cache($cacheName)){
            $this->apiError('发送验证码过于频繁');
        }
        $ret = EmailVerify::send($params['email']);
        if ($ret){
            cache($cacheName, 1, 60);
            $this->apiSuccess();
        }else{
            $this->apiError('发送失败，请重试！');
        }
    }

    /**
     * 校验邮件验证码
     */
    public function checkVerifyCode(){
        $params = get_params();
        $check = EmailVerify::check($params['code']);
        if( !$check['passed'])
        {
            $this->apiError('验证码错误失败');
        }
        $token = self::getEmailToken($check['email']);
        $this->apiSuccess(['token' => $token]);
    }

    /**
     * 回收重量排行(前100位)
     */
    public function rank(){
        $model = new RecycleOrder();
        $userModel = new User();
        $tableName = $model->getTable();
        $userTableName = $userModel->getTable();
        $where = [[$tableName.'.status','=',2],['approval_status','=',1]];
        $fields = 'first_name,last_name,SUM(weight) as total_weight';
        $list = Db::table($tableName)->alias('r')->join("$userTableName u","r.user_id = u.id")
            ->field($fields)->where($where)->group('user_id')->orderRaw("total_weight desc")->limit(100)->select();
        $this->apiSuccess($list);
    }
}
