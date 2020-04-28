<?php


namespace app\controllers;


use app\models\Goods;
use app\models\Token;
use Yii;

class GoodsController extends BaseController
{
    public function actionGoodsList(){
        $goodsList = Goods::getGoodsList();
        return $this->returnData(200,'success',$goodsList);
    }

    public function actionGoodsInfo(){
        //校验token
        $isToken = Token::checkToken();
        $goods_id = Yii::$app->request->get('goods_id');


    }
}