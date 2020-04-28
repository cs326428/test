<?php


namespace app\models;


use yii\db\ActiveRecord;
use app\common\Common;
use Yii;

class Token extends ActiveRecord
{
    public static $salt = 'sgdfhy546v34q3w4w5';

    public static function tableName()
    {
        return 'user';
    }

    public static function getDb()
    {
        //return Yii::$app->getDb('db2');
        return Yii::$app->db2;
    }

    /**
     * @param $username
     * @param $password
     * @return false|string|null
     */
    public static function getUserId($username, $password){
        $data = ['username'=>$username,'password'=>$password];
        $userid = self::find()->select('id')->where($data)->scalar();
        return $userid;
    }

    public static function createToken($userid){
        $expire = time()+3600;
        $str = sha1(uniqid().self::$salt.$expire);
        $token = base64_encode($userid.'='.$str);
        $redis = Common::redisObj();

//        $arr = ['NX', 'EX' => 3600];
//        $res = $redis->set('token:'.$userid,$token,$arr);
        $res = $redis->set('token:'.$userid,$token,3600);
        if($res){
            return true;
        }else{
            return false;
        }
    }

    public static function getToken($userid){
        $redis = Common::redisObj();
        $tokenExists = $redis->exists('token:'.$userid);
        if(!$tokenExists){
            self::createToken($userid);
        }
        $token = $redis->get('token:'.$userid);
        return $token;
    }

    /**
     * 校验token
     * @return bool
     */
    public static function checkToken(){
        $token = $_SERVER['HTTP_TOKEN'];
        $userId = self::getIdByToken();
        $redis = Common::redisObj();
        $token2 = $redis->get('token:'.$userId);
        if($token == $token2){
            $redis->expire('token:'.$userId,3600);
            return true;
        }else{
            return false;
        }
    }

    /**
     * 根据token获取用户id
     * @param string $token
     * @return bool
     */
    public static function getIdByToken($token=''){
        $token = $token ?: $_SERVER['HTTP_TOKEN'];
        $tokenStr = base64_decode($token);
        $offset = strpos($tokenStr,"=");
        $user_id = substr($tokenStr,0,$offset) ?: false;
        return $user_id;
    }

}