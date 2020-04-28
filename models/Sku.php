<?php


namespace app\models;

use yii\db\ActiveRecord;
use app\common\Common;
use Yii;

class Sku extends ActiveRecord
{
    public static function tableName()
    {
        return 'sku';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }


    /**
     * 根据skuId查询商品信息
     * @param $skuId
     * @return array|ActiveRecord|null
     */
    public static function getGoodsBySkuid($skuId){
        $res = self::find()->alias('s')
            ->leftJoin('goods g','s.goods_id=g.id')
            ->select(['g.goods_name','s.price','s.discount_price','s.stock'])
            ->where(['s.id'=>$skuId])
            ->asArray()
            ->one();
        return $res;
    }

    /**
     * 检查skuid是否存在
     * @param $skuId
     * @return bool
     */
    public static function existSkuId($skuId){
        $res = self::find($skuId)->exists();
        return $res;
    }

    /**
     * 获取库存
     * @param $skuId
     * @return false|string|null
     */
    public static function getStock($skuId){
        $num = self::find()
            ->select('num')
            ->where(['sku_id'=>$skuId])
            ->scalar();
        return $num;
    }

    /**
     * 查询商品id
     * @param $skuId
     * @return false|string|null
     */
    public static function getGoodsId($skuId){
        $goods_id = self::find()
            ->select('goods_id')
            ->where(['id'=>$skuId])
            ->scalar();
        return $goods_id;
    }
}