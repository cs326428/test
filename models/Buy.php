<?php


namespace app\models;


use yii\db\ActiveRecord;

class Buy extends ActiveRecord
{
    public static function tableName()
    {
        return 'buy';
    }
}