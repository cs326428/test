<?php


namespace app\common;

use Yii;
class Common {
    /**
     * 发送单人邮件
     * @param $toMember 收件人的地址
     * @param $subject  邮件主题
     * @param $content  邮件内容
     * @param bool $isHtml 邮件内容是否为html代码
     * @return array
     */
    public static function sendMail($toMember, $subject, $content, $isHtml=false){
        $obj = Yii::$app->mailer->compose();

        $obj->setTo($toMember);
        $obj->setSubject($subject);
        if($isHtml){
            $obj->setHtmlBody($content);
        }else{
            $obj->setTextBody($content);
        }
        $ret = $obj->send();
        if(!$ret){
            $mailInfo = [
                'toMember'  => $toMember,
                'subject'   => $subject,
                'content'   => $content,
                'isHtml'    => $isHtml,
                'sendTime'  => date('Y-m-d H:i:s',time()),
            ];
            Yii::error(json_encode($mailInfo),'mail');
            $result = ['code'=>500,'msg'=>'Mail sent failed'];
        }else{
            $result = ['code'=>200,'msg'=>'Mail sent successfully'];
        }
        return $result;
    }


    /**
     * 批量发送邮件
     * @param array $toMember
     * @param $subject
     * @param $content
     * @param bool $isHtml
     * @return array
     */
    public static function sendMultiple(array $toMember, $subject, $content, $isHtml=false){
        $obj = Yii::$app->mailer->compose();

        $messages = [];
        if($isHtml){
            $obj->setHtmlBody($content);
        }else{
            $obj->setTextBody($content);
        }
        $obj->setSubject($subject);

        foreach ($toMember as $member){
            $messages[] = $obj->setTo($member);
        }

        $ret = Yii::$app->mailer->sendMultiple($messages);

        if($ret){
            $result = ['code'=>200,'msg'=>'mail sent number: '.$ret];
        }else{
            $result = ['code'=>500,'msg'=>'Mail sent failed'];
        }

        return $result;
    }

    /**
     * redis实例
     * @param string $host
     * @param int $port
     * @param string $auth
     * @return \Redis
     */
    public static function redisObj($host='127.0.0.1', $port=6379, $auth=''){
        $redis = new \Redis();
        $redis->connect($host,$port);
        if($auth){
            $redis->auth($auth);
        }
        return $redis;
    }

    /**
     * redis并发锁
     * @param $lockKey 锁标识
     * @param int $timeout  锁生存时间
     * @return bool
     */
    public static function lock($lockKey, $timeout=3){
        $redis = self::redisObj();
        $lock = $redis->set($lockKey,time(),['NX','EX'=>$timeout]);
        if(!$lock){
            $lockTime = $redis->get($lockKey);
            if(!$lockTime){
                // 锁已过期，删除锁，重新获取
                self::unlock($lockKey);
                $lock = $redis->set($lockKey,time(),['NX','EX'=>$timeout]);
            }
        }
        return $lock ? true : false;
    }

    /**
     * 释放锁
     * @param $lockKey
     */
    public static function unlock($lockKey){
        $redis = self::redisObj();
        $redis->del($lockKey);
    }
}