<?php

namespace app\controllers;

use app\models\Register;
use Yii;
use yii\web\Controller;

class RegisterController extends Controller {

    //actions的作用主要是共用功能相同的方法
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'backColor'=>0x000000,//背景颜色
                'maxLength' =>  4,
                'minLength' =>  4,
                'padding' => 5,//间距
                'height'=>40,//高度
                'width' => 130,  //宽度
                'foreColor'=>0xffffff,     //字体颜色
                'offset'=>4,        //设置字符偏移量 有效果
                //'controller'=>'login',        //拥有这个动作的controller
            ],
        ];
    }

    public function actionRegister(){
//        if (!Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }

        $model = new Register();
        if ($model->load(Yii::$app->request->post(),'') && $model->register()) {
            //$this->redirect('/site/login');
            //延时跳转
            header("refresh:3;url=/site/login");
            echo '注册成功<br>'."<a href='/site/login'>五秒后自动跳转...</a>";
            exit;
        }

        //$model->password = '';
        return $this->render('register', [
            'model' => $model,
        ]);
    }
}

