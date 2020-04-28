<?php


namespace app\controllers;


use app\common\Common;
use app\models\Buy;
use app\models\token;
use yii\web\Controller;

class SeckillController extends Controller
{
    public function actionIndex(){
        phpinfo();
    }

    /**
     *生成库存队列
     */
    public function actionTest(){
        $get = \Yii::$app->request->get('type','');

        $redis = Common::redisObj();
        $store = 10;
        if($redis->lLen('goods_store')>0 || !empty($get)){
            echo $get;
            $goodsList = $redis->lRange('goods_store',0,-1);
            var_dump($goodsList);die;
        }
        for ($i=0;$i<$store;$i++){
            $redis->lPush('goods_store',1);
        }
        $goodsList = $redis->lRange('goods_store',0,-1);
        var_dump($goodsList);
    }

    /**
     * 消费库存队列
     * @return bool
     */
    public function actionBuy(){
        $redis = Common::redisObj();

        $len = $redis->lLen('goods_store');

        if($len<=0){
            echo '抢光了~';die;
        }

        $lock = Common::lock('test');
        //取锁3次，如果3次还未获取倒锁提示繁忙
        if(!$lock){
            for ($i=0;$i<3;$i++){
                if($lock){
                    break;
                }
                sleep(1);
            }
            if(!$lock){
                return false;
                //file_put_contents('D:\log1.txt', print_r('get lock fail!'."\r\n", true), FILE_APPEND | LOCK_EX);
            }
        }
        $goods = $redis->rPop('goods_store');
        //echo $goods."<br>";
        //file_put_contents('D:\log2.txt', print_r($goods."\r\n", true), FILE_APPEND | LOCK_EX);
        //保存抢购成功用户
        if($goods){
            $userObj = new Buy();
            $userObj->name = $goods;
            $userObj->save();
        }else{
            echo '抢光了~';die;
        }

    }


    public function actionBuy2(){
//        $lock = Common::lock('test');
//        //取锁3次，如果3次还未获取倒锁提示繁忙
//        if(!$lock){
//            for ($i=0;$i<3;$i++){
//                if($lock){
//                    break;
//                }
//                sleep(1);
//            }
//            if(!$lock){
//                return false;
//            }
//        }

        //从数据库获取库存数据
        $storeObj = token::findOne(1);
        file_put_contents('D:\log3.txt', print_r('1'."\r\n", true), FILE_APPEND | LOCK_EX);
        //库存
        $store = $storeObj->store;

        $redis = Common::redisObj();
        //用户队列
        $len = $redis->lLen('buy');
        //如果库存充足，允许用户抢购
        if($store-$len > 10){
            $redis->lPush('buy',mt_rand(1,9999));

            $user = $redis->rPop('buy');
            if($user){
                $storeObj->store = $storeObj->store-1;
                $storeObj->save();
                //保存抢购成功用户
                $userObj = new Buy();
                $userObj->name = $user;
                $userObj->save();
            }

        }else{
            echo '抢光了~';
        }
    }


}