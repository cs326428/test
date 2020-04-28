<?php


namespace app\models;


use yii\db\ActiveRecord;
use Yii;

class Order extends ActiveRecord
{
    public static function tableName()
    {
        return 'order';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }

    /**
     * 添加订单
     * @param $orderInfo
     * @return array
     */
    public static function createOrder($orderInfo){
        //var_dump($orderInfo);die;
        $transaction = self::getDb()->beginTransaction();
        try{
            $order = new self();
            $order->order_id = $orderInfo['order_id'];
            $order->user_id = Token::getIdByToken();
            $order->discount_price = $orderInfo['total']-$orderInfo['real_total'];
            $order->total_price = $orderInfo['total'];
            $order->disbursement = $orderInfo['real_total'];
            $order->status = 1;
            $order->save();

            //添加数据到order_info表
            $orderInfoObj = new OrderInfo();
            foreach($orderInfo['goodsList'] as $value){
                $obj = clone $orderInfoObj;
                $obj->order_id = $orderInfo['order_id'];
                $obj->sku_id = $value['sku_id'];
                $obj->num = $value['num'];
                $obj->discount_price = $value['discount_price'];
                $obj->price = $value['real_price'];
                $obj->goods_id = Sku::getGoodsId($value['sku_id']);
                $obj->save();
            }

            $transaction->commit();
            return ['code'=>200,'msg'=>'success'];
        }catch (\Exception $e){
            $transaction->rollBack();
            return ['code'=>100062,'msg'=>'添加订单失败'.$e->getMessage().' line: '.$e->getLine()];
        }
    }

    /**
     * 修改订单状态
     * @param $orderid
     * @param $status
     * @return bool
     */
    public static function updateOrderStatus($orderid, $status){
        $order = self::find()->where(['order_id'=>$orderid])->one();
        $order->status = $status;
        return $order->save();
    }
}