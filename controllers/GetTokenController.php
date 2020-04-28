<?php


namespace app\controllers;

use app\models\Token;
use app\service\loginsubject\LoginLog;
use app\service\loginsubject\LoginSubject;
use app\service\loginsubject\RemindFriends;
use Yii;

class GetTokenController extends BaseController
{
    /**
     * 获取token
     * @return array
     */
    public function actionGetToken(){
        $post = Yii::$app->request->post();
        $userid = token::getUserId($post['username'],$post['password']);
        if(!$userid){
            return $this->returnData(10001,'用户名或密码错误');
        }else{
            $token = token::getToken($userid);

            //触发登录事件
            $loginSubject = new LoginSubject();
            $loginSubject->register(new LoginLog());
            $loginSubject->register(new RemindFriends());
            $loginSubject->notify();

            return $this->returnData(200,'success',['token'=>$token]);
        }
    }
}