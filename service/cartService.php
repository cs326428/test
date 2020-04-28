<?php

namespace app\service;

use app\common\Common;
use app\lib\cartLib;
use app\models\Token;
use app\models\Cart;

class cartService
{
    protected $redis;
    protected $userId;
    //用户购物车key
    protected $preKey;

    public function __construct()
    {
        $this->redis = Common::redisObj();
        $this->userId = Token::getIdByToken();
        $this->preKey = 'cart:'.$this->userId;
    }

    /**
     * 购物车内全部商品信息
     * @return array|string|\yii\db\ActiveRecord[]
     */
    public function getCartInfo(){
        $cartLib = new cartLib();
        //缓存购物车是否存在
        $existsCart = $cartLib->existsCart();
        if(!$existsCart){
            //从数据库获取购物车信息
            $CartInfo = Cart::getCartList();
            if(!empty($CartInfo)){
                foreach($CartInfo as $key=>$value){
                    $tmp1 = [
                        $value['sku_id']=>$value['num']
                    ];

                    $tmp2 = [
                        'goods_name' => $value['goods_name'],
                        'price' => $value['price']
                    ];
                    //数据库中的购物车信息存入缓存购物车
                    $this->redis->multi();
                    //购物车商品规格数量
                    $this->redis->hMSet($this->preKey,$tmp1);
                    //购物车商品详情
                    $this->redis->hMSet($value['sku_id'],$tmp2);
                    $this->redis->exec();
                }
            }
        }else{
            //获取缓存购物车全部商品
            $goodsArr = $cartLib->getAll();
            //购物车商品详细信息
            $CartInfo = [];
            foreach($goodsArr as $key=>$value){
                $goodsName = $this->redis->hGet($key,'goods_name');
                $price = $this->redis->hGet($key,'price');
                $CartInfo[] = [
                    'sku_id' => $key,
                    'num' => $value,
                    'goods_name' => $goodsName,
                    'price' => $price,
                ];
            }
        }

        return $CartInfo;
    }

    /**
     * 购物车 添加(减少)商品
     * @param $skuId
     * @param int $num 可以为负值
     * @return bool|int
     */
    public function addCart($skuId, $num = 1){
        //增加商品到(数据库)购物车
        $res = Cart::addCart($skuId,$num);
        if($res){
            //增加商品到(redis)购物车
            $cartLib = new cartLib();
            $result = $cartLib->addCart($skuId, $num);
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * 删除单个商品
     * @param $skuId
     * @return bool|int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delGoods($skuId){
        $res = Cart::delGoods($skuId);
        if($res){
            $cartLib = new cartLib();
            $result = $cartLib->delete($skuId);
        }else{
            $result = false;
        }
        return $result;
    }


    /**
     * 删除购物车多个商品
     * @param $skuIdArr
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delBatch($skuIdArr){
        $res = Cart::delGoodsArr($skuIdArr);
        if($res){
            $cartLib = new cartLib();
            $result = $cartLib->deleteBatch($skuIdArr);
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * 清空数据库
     * @return bool|int
     */
    public function delAll(){
        $res = Cart::delAll();
        if($res){
            $cartLib = new cartLib();
            $result = $cartLib->deleteAll();
        }else{
            $result = false;
        }
        return $result;
    }
}