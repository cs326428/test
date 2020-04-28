<?php


namespace app\commands;


use app\common\Common;
use app\models\Order;
use app\models\Token;
use yii\console\Controller;

class OrderController extends Controller
{
    protected $redis;
    //redis中待支付订单有序集合key
    protected $paykey;
    //redis中待支付订单有序集合score
    protected $score;
    //redis中待支付订单详情key
    protected $orderInfoKey;

    public function __construct()
    {
        $this->redis = Common::redisObj();
        $this->paykey = 'no_payment';
        $this->score = time()+15*60;
        $this->orderInfoKey = 'no_payment_order_info:';
    }

    /**
     * 订单超时未支付 自动取消
     * @return array
     */
    public function actionDelOrder(){
        //已超时订单
        $arr = $this->redis->zRangeByScore($this->paykey,0,time());
        //删除已超时订单
        $count = $this->redis->zRemRangeByScore($this->paykey,0,time());
        //删除已超时订单详情
        $this->redis->delete($arr);


        $tmp = [];
        //修改数据库中订单状态为取消
        foreach($arr as $key=>$value){
            $res = Order::updateOrderStatus($value,3);
            if(!$res){
                \Yii::info('订单id：'.$value.'状态修改为 已取消 失败');
                //状态更改失败订单数据
                $tmp[] .= $value;
            }
        }
        return ['code'=>200,'msg'=>'已自动取消'.$count.'条订单','data'=>$tmp];

    }
}