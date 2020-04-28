<?php


namespace app\service;


use app\common\Common;
use app\models\Order;
use app\models\Sku;
use app\models\Token;

class orderService
{
    protected $redis;
    protected $userId;
    //redis中待支付订单有序集合key
    protected $paykey;
    //redis中待支付订单有序集合score
    protected $score;
    //redis中待支付订单详情key
    protected $orderInfoKey;

    public function __construct()
    {
        $this->redis = Common::redisObj();
        $this->userId = Token::getIdByToken();
        $this->paykey = 'no_payment';
        $this->score = time()+15*60;
        $this->orderInfoKey = 'no_payment_order_info:';
    }

    /**
     * 生成订单号
     * @param $info
     * @return string
     */
    public function createOrderid(){
        return $orderId = date('YmdHis') . rand(10000,99999);;
    }

    /**
     * 问题：怎么保证redis和mysql都成功执行 line81~88
     * 提交订单
     * @param array $goodsArr
     * @return array
     */
    public function createOrder(array $goodsArr){
        $total = 0;
        $realTotal = 0;
        $orderInfo = [];
        foreach($goodsArr as $key => $value){
            $sku_id = $value['sku_id'];
            $num = $value['num'];
            //商品详细信息
            $goodsinfo = Sku::getGoodsBySkuid($sku_id);
            if($num > $goodsinfo['stock']){
                return ['code'=>100061,'msg'=>'sku_id: ' .$sku_id.' 库存不足'];
            }else{
                //如果存在优惠价以优惠价为准
                $realPrice = empty($goodsinfo['discount_price']) ?: $goodsinfo['price'];
                $goodsList = [
                    'sku_id' => $sku_id,
                    'num' => $num,
                    'goods_name' => $goodsinfo['goods_name'],
                    'price' => $goodsinfo['price'],
                    'discount_price' => $goodsinfo['discount_price'],
                    'real_price' => $realPrice
                ];
                $orderInfo['goodsList'][] = $goodsList;
                //总价
                $total += $goodsinfo['price'] * $num;
                //实付款
                $realTotal += $realPrice * $num;
            }
        }

        $orderInfo['order_id'] = $this->createOrderid();
        $orderInfo['creat_time'] = date('Y-m-d H:i:s');
        $orderInfo['total'] = $total;
        $orderInfo['real_total'] = $realTotal;

        $this->redis->multi();
        //订单写入redis待支付订单集合   消费这个集合的方法是在\commands\Order\DelOrder
        $this->noPaymentOrder($orderInfo['order_id']);
        //待支付订单详情
        $this->noPaymentOrderInfo($orderInfo);
        $this->redis->exec();
        //写入mysql
        $result = Order::createOrder($orderInfo);
        return $result;
    }

    /**
     * 订单写入redis待支付订单集合
     * @param $orderid
     * @return int
     */
    public function noPaymentOrder($orderid){
        return $this->redis->zAdd($this->paykey,$this->score,$orderid);
    }

    /**
     * 待支付订单详情
     * @param array $orderInfo
     * @return bool
     */
    public function noPaymentOrderInfo(array $orderInfo){
        if(!is_array($orderInfo) || empty($orderInfo)){
            return false;
        }
        $orderStr = json_encode($orderInfo);
        return $this->redis->setnx($this->orderInfoKey.$orderInfo['order_id'],$orderStr);
    }
}