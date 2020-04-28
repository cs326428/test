<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    /*
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];
    */

    public static function tableName(){
        return 'user';
    }

    /**
     * 根据指定的用户ID查找 认证模型类的实例，
     * 当你需要使用session来维持登录状态的时候会用到这个方法。
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
        //return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * 根据指定的存取令牌查找 认证模型类的实例，
     * 该方法用于 通过单个加密令牌认证用户的时候（比如无状态的RESTful应用）。
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
//        foreach (self::$users as $user) {
//            if ($user['accessToken'] === $token) {
//                return new static($user);
//            }
//        }
//
//        return null;
    }

    /**
     * Finds user by username
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
//        foreach (self::$users as $user) {
//            if (strcasecmp($user['username'], $username) === 0) {
//                return new static($user);
//            }
//        }
        $user = self::find()->where(['username'=>$username])->one();

        return $user;
    }

    /**
     * 获取该认证实例表示的用户的ID。
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 获取基于 cookie 登录时使用的认证密钥。
     * 认证密钥储存在 cookie 里并且将来会与服务端的版本进行比较以确保 cookie的有效性。
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        //return $this->authKey;
        return $this->auth_key;
    }

    /**
     * 是基于 cookie 登录密钥的 验证的逻辑的实现
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        //return $this->password === $password;
        return  Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * 为model的password_hash字段生成密码的hash值
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * 生成 "remember me" 认证key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
}
