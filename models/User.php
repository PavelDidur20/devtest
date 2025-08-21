<?php

namespace app\models;

use yii\db\ActiveRecord;


/**
 * Это класс модели для таблицы "users".
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string $auth_key
 * @property string $role
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const ROLE_MANAGER = 'manager';
    const ROLE_CLIENT = 'client';

    public static function tableName()
    {
        return '{{%users}}'; 
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
           return static::findOne(['accessToken' => $token]);
    }

    /**
     * Найти пользователя 
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
       return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
         return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function rules()
    {
        return [
            [['username', 'password_hash', 'auth_key', 'role'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'auth_key'], 'string', 'max' => 255],
            [['role'], 'string', 'max' => 20],
            [['role'], 'in', 'range' => [self::ROLE_MANAGER, self::ROLE_CLIENT]],
            [['username'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'role' => 'Role',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function isManager()
    {
        return $this->role === self::ROLE_MANAGER;
    }

     public function isClient()
    {
        return $this->role === self::ROLE_CLIENT;
    }
}
