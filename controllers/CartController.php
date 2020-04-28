<?php


namespace app\controllers;


use app\common\Common;
use app\models\Cart;
use app\models\Token;
use app\service\cartService;
use Yii;

class CartController extends BaseController
{
    public function __construct($id, $module, $config = [])
    {
        $checkToken = Token::checkToken();
        if(!$checkToken){
            return $this->returnData(10000,'token is invalid');
        }
        parent::__construct($id, $module, $config);
    }

    /**
     * 购物车内全部商品信息
     * @return array
     */
    public function actionGetCartInfo(){
        $cartService = new cartService();
        $CartInfo = $cartService->getCartInfo();
        return $this->returnData(200,'success',$CartInfo);
    }

    /**
     * 购物车 添加(减少)商品
     * @return array
     */
    public function actionAddCart(){
        $post = Yii::$app->request->post();
        $skuId = intval($post['sku_id'] ?? 0);
        $num = intval($post['num'] ?? 1);
        if(!$skuId || !is_numeric($skuId) || !$num || !is_numeric($num) || !is_int($num)){
            return $this->returnData(100030,'参数错误');
        }else{
            $cartService = new cartService();
            $result = $cartService->addCart($skuId,$num);
            if(!$result){
                return $this->returnData(100040,'添加失败');
            }
            return $this->returnData(200,'添加成功');
        }
    }

    /**
     * 移出购物车一件商品
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelGoods(){
        $post = Yii::$app->request->post();
        $skuId = intval($post['sku_id'] ?? 0);
        if(!$skuId || !is_numeric($skuId)){
            return $this->returnData(100030,'参数错误');
        }else{
            $cartService = new cartService();
            $cartService->delGoods($skuId);
            return $this->returnData(200,$skuId.'已移出购物车');
        }
    }

    /**
     * 批量删除购物车内商品
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelBatch(){
        $post = Yii::$app->request->post();
        $skuIdArr = $post['skuIdArr'] ?? '';
        if(!is_array($skuIdArr)){
            return $this->returnData(100030,'参数错误');
        }else{
            $cartService = new cartService();
            $cartService->delBatch($skuIdArr);
            return $this->returnData(200,count($skuIdArr).'件商品已移出购物车');
        }
    }

    /**
     * 清空购物车
     * @return array
     */
    public function actionDelAll(){
        $cartService = new cartService();
        $cartService->delAll();
        return $this->returnData(200,'已清空购物车');
    }
}