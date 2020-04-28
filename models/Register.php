<?php


namespace app\models;


use yii\db\ActiveRecord;

class Register extends ActiveRecord
{
    public $username;
    public $password;
    public $repassword;
    public $email;
    public $verifyCode;

    public function rules()
    {
        return [
            [['username', 'password','repassword','email','verifyCode'], 'required'],
            ['username','unique','message'=>'用户名已存在~'],
            ['email','email'],
            [['username', 'password','repassword'], 'trim'],
            ['repassword', 'compare', 'compareAttribute' => 'password','message'=>'不一致'],
            //注意captchaAction的设置，指向你显示验证码的action
            ['verifyCode', 'captcha', 'captchaAction' => 'register/captcha', 'caseSensitive' => false, 'message' => '验证码错误'],
            //该验证器并不进行数据验证。而是把一个属性标记为 安全属性。
            ['repassword','safe'],
            ['verifyCode','safe'],
        ];
    }
    public static function tableName()
    {
        return 'user';
    }

    public function register(){
        if (!$this->validate()) {
            if ($this->hasErrors()) {
                $errorArr = $this->getFirstErrors();
                $message =array_shift($errorArr);
                if(!empty($message)){
                    \Yii::$app->session->setFlash('errorMsg',$message);
                }
            }
        }else{
            // 实现数据入库操作
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;

            // 设置密码，密码肯定要加密，暂时我们还没有实现，看下面我们有实现的代码
            $user->setPassword($this->password);

            // 生成 "remember me" 认证key
            $user->generateAuthKey();

            // save(false)的意思是：不调用UserBackend的rules再做校验并实现数据入库操作
            // 这里这个false如果不加，save底层会调用UserBackend的rules方法再对数据进行一次校验，因为我们上面已经调用Signup的rules校验过了，这里就没必要在用UserBackend的rules校验了
            return $user->save(false);
        }

    }
}