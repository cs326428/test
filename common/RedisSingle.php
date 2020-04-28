<?php
namespace app\common;


class RedisSingle
{
    private static $_instance;

    //禁止直接创建对象
    private function __construct($host='127.0.0.1', $port=6379, $auth='')
    {
        $redis = new \Redis();
        $redis->connect($host,$port);
        if($auth){
            $redis->auth($auth);
        }
        self::$_instance = $redis;
    }

    public static function getInstance(){
        if(!self::$_instance){
            new self();
        }

        return self::$_instance;
    }

    //阻止用户克隆对象实例
    private function __clone()
    {
        var_dump(trigger_error(__CLASS__." is single mode and can't be clone!",E_USER_ERROR));
    }
}