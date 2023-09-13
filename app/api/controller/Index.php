<?php

declare (strict_types = 1);
namespace app\api\controller;

use app\admin\model\DonateRecord;
use app\api\BaseController;
use app\api\middleware\EmailAuth;
use app\api\validate\IndexCheck;
use app\facade\EmailVerify;
use app\model\RecycleOrder;
use app\model\User;
use Baiy\ThinkAsync\Facade\Async;
use think\App;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Log;


class Index extends BaseController
{
    /**
     * 控制器中间件 [登录、注册 不需要鉴权]
     * @var array
     */
	protected $middleware = [
    	EmailAuth::class => ['only' => ['resetPassword']]
    ];
	

    public function index()
    {
        return redirect('/index.html');
    }

    /**
     * 获取stripe收款client_secret
     */
    public function getStripeKey()
    {
        $param = get_params();
        try {
            validate(IndexCheck::class)->scene(request()->action())->check($param);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        $config = get_system_config('stripe');
        if (empty($config)){
            Log::error('未配置stripe',['config_name'=>'stripe']);
            $this->apiError('System error, please try later.');
        }
        $ip = app('request')->ip();
        $stripe = new \Stripe\StripeClient($config['secret_key']);
        $ret = $stripe->paymentIntents->create(
            ['amount' => $param['amount'] * 100, 'currency' => 'gbp', 'automatic_payment_methods' => ['enabled'=>true]]
        );
        $model = new DonateRecord();
        $clientSecret = $model->genRecord($param,$config['secret_key'],$ip);
        if(!$ret){
            $this->apiError('System error, please try later.');
        }
        $data = ['client_secret'=>$clientSecret,'public_key'=>$config['public_key']];
        $this->apiSuccess($data);
    }

    /**
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
		$user = User::where('email',$param['email'])->find();
        if (!$user) {
            $this->apiError("Account doesn't exist");
        }
        $param['pwd'] = set_password($param['password'], $user['salt']);
        if ($param['pwd'] !== $user['password']) {
            $this->apiError('Password not correct');
        }
        if ($user['status'] == -1) {
            $this->apiError('Not permitted to log in, please contact the platform.');
        }
        $data = [
            'last_login_time' => time(),
            'last_login_ip' => request()->ip(),
            'login_num' => $user['login_num'] + 1,
        ];
        $res = $user->save($data);
        if ($res) {
            $token = self::getToken($user['id']);
            cookie('token',$token);
            $this->apiSuccess();
        }
    }

    /**
     * @api {post} /index/reg 会员注册
     */
    public function reg()
    {
		$param = get_params();
        Log::error('新用户注册'.json_encode($param));
        try {
            validate(IndexCheck::class)->scene(request()->action())->check($param);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        $check = EmailVerify::check($param['code'],$param['email']);
        if( !$check)
        {
            $this->apiError('Verification code not correct');
        }
		$user = User::where('email',$param['email'])->find();
        if ($user) {
			$this->apiError('This email has been registered');
        }
        $param['salt'] = set_salt(20);
        $param['password'] = set_password($param['password'], $param['salt']);
        $param['register_time'] = time();
        $param['last_login_time'] = time();
        $param['headimgurl'] = '/static/admin/images/icon.png';
        $param['register_ip'] = request()->ip();
        $param['last_login_ip'] = request()->ip();
        $param['approval_status'] = $param['type'] == 2 ? 0 : 1;
        $uid = User::strict(false)->field(true)->insertGetId($param);
		if($uid){
		    $userNo = User::buildNo($uid);
		    User::where('id',$uid)->update(['user_no'=>$userNo]);
            $token = self::getToken($uid);
            cookie('token',$token);
            $this->apiSuccess();
		}else{
			$this->apiError('System error, please try later.');
		}
    }

    /**
     * 重置密码
     */
    public function resetPassword()
    {
        $param = get_params();
        try {
            validate(IndexCheck::class)->scene(request()->action())->check($param);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        $user = User::where('email', $param['email'])->find();
        if (!$user) {
            $this->apiError("Account doesn't exist");
        }
        $salt = set_salt(20);
        $password = set_password($param['password'], $salt);

        $updateData = ['password' => $password,'salt' => $salt];
        $uid = User::where('id',$user['id'])->update($updateData);
        if($uid){
            $this->apiSuccess();
        }else{
            $this->apiError('System error, please try later.');
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
        $cacheName = 'email_request_limit'.base64_encode($params['email']);
        if (get_cache($cacheName)){
            $this->apiError('Verification codes are sent too often');
        }

        $user = User::where('email',$params['email'])->find();

        if ($params['tag'] == 'resetPassword' && !$user){
            $this->apiError("Account doesn't exist");
        }
        if ($params['tag'] == 'reg' && $user){
            $this->apiError('This email has been registered');
        }
        Async::trigger('send_email_verify_code',$params['email']);
        cache($cacheName, 1, 60);
        $this->apiSuccess();
    }

    /**
     * 校验邮件验证码
     */
    public function checkVerifyCode(){
        $param = get_params();
        try {
            validate(IndexCheck::class)->scene(request()->action())->check($param);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        $check = EmailVerify::check($param['code'],$param['email']);
        if( !$check)
        {
            $this->apiError('Verification code not correct');
        }
        $token = self::getEmailToken($param['email']);
        cookie('token',$token);
        $this->apiSuccess();
    }

    /**
     * 积分排行(前100位)
     */
    public function rank(){
        $model = new RecycleOrder();
        $userModel = new User();
        $tableName = $model->getTable();
        $userTableName = $userModel->getTable();
        $where = [[$tableName.'.status','=',2],['approval_status','=',1]];
        $fields = "first_name,last_name,headimgurl,city,SUM(r.points) as total_points";
        $list = Db::table($tableName)->alias('r')->join("$userTableName u","r.user_id = u.id")
            ->field($fields)->where($where)->group('user_id')->orderRaw("total_points desc")->limit(100)->select();
        $this->apiSuccess($list);
    }

    public function listEnum(){
        $data = [
            'order_status' => \app\admin\model\RecycleOrder::$statusEnum,
            'voucher_status' => \app\model\Voucher::$statusEnum,
            'points_record_status' => \app\admin\model\PointsRecord::$statusEnum,
            'points_record_type' => \app\admin\model\PointsRecord::$typeEnum,
        ];
        $this->apiSuccess($data);
    }

    public function listMerchants(){
        $where = [['type','=',2],['approval_status','=',1]];
        $fields = ['first_name','last_name','longitude','latitude','company','headimgurl','city','address','detail_address','postcode'];
        $limit = 999;
        $list = User::where($where)->field($fields)->limit($limit)->select()->toArray();
        $this->apiSuccess($list);
    }
}
