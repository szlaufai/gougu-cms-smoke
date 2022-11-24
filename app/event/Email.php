<?php
declare (strict_types = 1);

namespace app\event;

use app\facade\EmailVerify;
use think\facade\Log;

class Email
{
    public static function donateSucceed($email){
        $title = 'GreenWings感谢您的捐赠，请勿回复';
        $content = '祝您好运！';
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
