<?php

namespace app\api\middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Cookie;

class EmailAuth
{
    public function handle($request, \Closure $next)
    {
        $token = Cookie::get('token');
        if ($token) {
            if (count(explode('.', $token)) != 3) {
                return json(['code'=>404,'msg'=>'非法请求']);
            }
			$config = get_system_config('token');
            try {
				JWT::$leeway = 60;//当前时间减去60，把时间留点余地
					$decoded = JWT::decode($token, new Key($config['secrect'], 'HS256')); //HS256方式，这里要和签发的时候对应
					$decoded_array = json_decode(json_encode($decoded),TRUE);
					$jwt_data = $decoded_array['data'];
					if(get_params('email') != $jwt_data['email']){
                        return json(['code'=>1,'msg'=>'参数错误']);
                    };
					return $next($request);
				} catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
					return json(['code'=>403,'msg'=>'签名错误']);
				}catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
					return json(['code'=>401,'msg'=>'token失效']);
				}catch(\Firebase\JWT\ExpiredException $e) {  // token过期
					return json(['code'=>401,'msg'=>'token已过期']);
				}catch(Exception $e) {  //其他错误
					return json(['code'=>404,'msg'=>'非法请求']);
				}catch(\UnexpectedValueException $e) {  //其他错误
					return json(['code'=>404,'msg'=>'非法请求']);
				} catch(\DomainException $e) {  //其他错误
					return json(['code'=>404,'msg'=>'非法请求']);
				}
        } else {
            return json(['code'=>404,'msg'=>'token不能为空']);
        }
    }
}