<?php

namespace app\lib;

use app\common\Common;
use app\models\Token;

class cartLib
{
    protected $redis;
    protected $userId;
    protected $preKey;

    public function __construct()
    {
        $this->redis = Common::redisObj();
        $this->userId = Token::getIdByToken();
        $this->preKey = 'cart:'.$this->userId;
    }

    /**
     * 检查购物车是否存在
     * @return int
     */
    public function existsCart(){
        return $this->redis->exists($this->preKey);
    }
    /**
     * 购物车商品数量
     * @return int
     */
    public function getCartNum(){
        return $this->redis->hLen($this->preKey);
    }

    /**
     * 获取购物车某商品的数量
     * @param $skuId
     * @return string
     */
    public function getGoodsNum($skuId){
        return $this->redis->hGet($this->preKey, $skuId);
    }

    /**
     * 获取购物车全部商品信息
     * @return array
     */
    public function getAll(){
        return $this->redis->hGetAll($this->preKey);
    }

    /**
     * 获取购物车全部商品skuId
     * @return array
     */
    public function getAllKeys(){
        return $this->redis->hkeys($this->preKey);
    }

    /**
     * 检查商品是否存在购物车
     * @param $skuId
     * @return bool
     */
    public function exists($skuId){
        return $this->redis->hExists($this->preKey, $skuId);
    }

    /**
     * 加入购物车，移除购物车，但是不会删除
     * @param $skuId
     * @param int $num
     * @return int
     */
    public function addCart($skuId, $num = 1){
        return $this->redis->hIncrBy($this->preKey,$skuId,$num);
    }

    /**
     * 删除单个商品
     * @param $skuId
     * @return bool|int
     */
    public function delete($skuId){
        return $this->redis->hDel($this->preKey, $skuId);
    }

    /**
     * 删除多个商品
     * @param $skuIdArr
     * @return bool
     */
    public function deleteBatch($skuIdArr){
        foreach ($skuIdArr as $k => $v) {
            $this->redis->hDel($this->preKey, $v);
        }

        return true;
    }

    /**
     * 清空购物车
     * @return int
     */
    public function deleteAll(){
        return $this->redis->del($this->preKey);
    }
}