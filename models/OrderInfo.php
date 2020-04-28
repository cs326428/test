<?php


namespace app\models;


use yii\db\ActiveRecord;
use Yii;

class OrderInfo extends ActiveRecord
{
    public static function tableName()
    {
        return 'order_info';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }


}