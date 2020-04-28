<?php

namespace app\controllers;

use app\common\Common;
use app\common\OperationFactory;
use app\common\RedisSingle;

class IndexController extends BaseController {
    public function actionMail(){
        $toMember = ['cs326428@163.com'];
        $subject = 'Yii mail';
        $content = 'This is my first Email';
        $res = Common::sendMultiple($toMember, $subject, $content);
        return $res;
    }

    public function actionIndex(){
        $redis = RedisSingle::getInstance();
        $redis->set('a','111');
        $redis->get('a');
    }

    public function actionOperation(){
//        $response = \Yii::$app->response;
//        $response->format = \yii\web\Response::FORMAT_RAW;
        $operationObj = OperationFactory::createObj('+');
        $res = $operationObj->getValue(1,1);
        echo $res;
    }


}

