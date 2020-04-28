<?php


namespace app\controllers;


use yii\web\Controller;
use Yii;

class BaseController extends Controller {
    public function __construct($id, $module, $config = []) {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        parent::__construct($id, $module, $config);
    }

    /**
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return array
     */
    public function returnData($code=200, $msg='success', $data=[]){
        if(empty($data)){
            $data =  ['code'=>$code,'msg'=>$msg];
        }else{
            $data =  ['code'=>$code,'msg'=>$msg,'data'=>$data];
        }
        return $data;

    }
}