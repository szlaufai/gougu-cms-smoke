<?php

namespace app\api\middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Cookie;
use think\facade\Env;
use think\facade\Request;

class Auth
{
    public function handle($request, \Closure $next)
    {
        if (Env::get('app_local')){ //本地调试时不鉴权，默认ID为1的用户
            define('JWT_UID', 1);
            return $next($request);
        }
        $token = Cookie::get('token');
        if ($token) {
            if (count(explode('.', $token)) != 3) {
                return json(['code'=>404,'msg'=>'Illegal request']);
            }
			$config = get_system_config('token');
            try {
				JWT::$leeway = 60;//当前时间减去60，把时间留点余地
					$decoded = JWT::decode($token, new Key($config['secrect'], 'HS256')); //HS256方式，这里要和签发的时候对应
					$decoded_array = json_decode(json_encode($decoded),TRUE);
					$jwt_data = $decoded_array['data'];
					define('JWT_UID', $jwt_data['userid']);
					return $next($request);
				} catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
					return json(['code'=>403,'msg'=>'Signature error']);
				}catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
					return json(['code'=>401,'msg'=>'Token failure']);
				}catch(\Firebase\JWT\ExpiredException $e) {  // token过期
					return json(['code'=>401,'msg'=>'Token expired']);
				}catch(Exception $e) {  //其他错误
					return json(['code'=>404,'msg'=>'Illegal request']);
				}catch(\UnexpectedValueException $e) {  //其他错误
					return json(['code'=>404,'msg'=>'Illegal request']);
				} catch(\DomainException $e) {  //其他错误
					return json(['code'=>404,'msg'=>'Illegal request']);
				}
        } else {
            return json(['code'=>404,'msg'=>'token is required']);
        }
    }
}