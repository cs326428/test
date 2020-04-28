<?php


namespace app\models;


use yii\db\ActiveRecord;
use Yii;

class Goods extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db2;
    }

    public static function getGoodsList(){
        $gooodsList = self::find()->asArray()->all();
        return $gooodsList;
    }

    public static function getGoodsInfo($goodsId){
        $info = self::find()->asArray()->one();
        return $info;
    }
}