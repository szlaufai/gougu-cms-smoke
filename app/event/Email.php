<?php
declare (strict_types = 1);

namespace app\event;

use app\facade\EmailVerify;
use think\facade\Log;

class Email
{
    public static function donateSucceed($email,$amount){
        $title = 'Your donation is successful';
        $content = "Your donation of £ $amount is successful, we really appreciate your kindness and thank you for making the world better.";
        try {
            send_email($email,$title,$content);
            return true;
        }catch (\Exception $e){
            Log::error('发送邮件异常'.json_encode(['error'=>$e->getMessage()]));
            return false;
        }
    }

    public static function verifyCode($email){
        EmailVerify::send($email);
    }
}
