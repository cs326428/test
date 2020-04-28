<?php


namespace app\controllers;

use app\common\Common;
use app\models\Token;
use app\service\orderService;
use Yii;

class OrderController extends BaseController
{
    private $redisObj;
    private $user_id;
    public function __construct($id, $module, $config = [])
    {
        $this->redisObj = Common::redisObj();
        $this->user_id = Token::getIdByToken();
        parent::__construct($id, $module, $config);
    }

    /**
     * 生成待确认订单
     * @return array
     */
    public function actionCreateOrder(){
        $post = Yii::$app->request->post();
        $goodsArr = $post['goods'] ?? [];
        if(empty($goodsArr)){
            return $this->returnData(100060,'参数错误');
        }

        $orderService = new orderService();
        return $orderService->createOrder($goodsArr);
    }


}