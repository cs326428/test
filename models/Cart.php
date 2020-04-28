<?php


namespace app\models;

use yii\db\ActiveRecord;
use app\common\Common;
use Yii;

class Cart extends ActiveRecord
{
    public static function tableName()
    {
        return 'cart';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }

    /**
     * 获取购物车信息
     * @param $userId
     * @return array|string|ActiveRecord[]
     */
    public static function getCartList($userId=''){
        $userId = $userId ?: Token::getIdByToken();
        if($userId){
            $list = self::find()->alias('c')
                ->select(['c.sku_id','c.num','g.goods_name','s.price'])
                ->where(['user_id'=>$userId])
                ->leftJoin(['s'=>'sku'],'c.sku_id=s.id')
                ->leftJoin(['g'=>'goods'],'s.goods_id=g.id')
                ->orderBy('c.create_time desc')
                ->asArray()
                ->all();
        }
        return $list ?? '';
    }

    /**
     * 获取购物车（一件）商品的详情
     * @param $skuId
     * @return array|ActiveRecord|null
     */
    public static function getGoodsInfo($skuId){
        $userId = Token::getIdByToken();
        $cart = self::find()
            ->where(['user_id'=>$userId,'sku_id'=>$skuId])
            ->one();
        return $cart;
    }

    /**
     * 新增购物车商品
     * @param $skuId
     * @return bool
     */
    public static function addCart($skuId,$num){
        $userId = Token::getIdByToken();
        //检查skuId是否合法
        $res = Sku::existSkuId($skuId);
        if(!$res){
            return false;
        }
        //商品是否在购物车
        $existCart = self::existCart($skuId);
        if(!$existCart){
            $cart = new self();
            $cart->user_id = $userId;
            $cart->sku_id = $skuId;
        }else{
            //获取购物车（一件）商品的详情
            $cart = self::getGoodsInfo($skuId);
        }
        $cart->num = $num;
        $re = $cart->save();
        return $re;
    }


    /**
     * 删除数据库中购物车内(一件)商品
     * @param $skuId
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function delGoods($skuId){
        $userId = Token::getIdByToken();
        //检查skuId是否合法
        $res = Sku::existSkuId($skuId);
        //商品是否在购物车
        $existCart = self::existCart($skuId);
        if($existCart){
            $info = self::getGoodsInfo($skuId);
            $info->delete();
        }
        return true;
    }

    /**
     * 删除购物车(多个)商品
     * @param $skuIdArr
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function delGoodsArr($skuIdArr){
        foreach($skuIdArr as $key=>$value){
            //检查skuId是否合法
            $res = Sku::existSkuId($value);
            //商品是否在购物车
            $existCart = self::existCart($value);
            if($res && $existCart){
                $info = self::getGoodsInfo($value);
                $info->delete();
            }
        }
        return true;
    }

    /**
     * 清空数据库
     * @return int
     */
    public static function delAll(){
        $userId = Token::getIdByToken();
        return self::deleteAll(['user_id'=>$userId]);
    }

    /**
     * 商品是否存在于购物车
     * @param $skuId
     * @return bool
     */
    public static function existCart($skuId){
        $userId = Token::getIdByToken();
        $existCart = self::find()
            ->where(['user_id'=>$userId,'sku_id'=>$skuId])
            ->exists();
        return $existCart;
    }
}