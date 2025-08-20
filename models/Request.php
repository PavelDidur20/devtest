<?php
// models/Request.php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Это класс модели для таблицы "request".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $status
 * @property string $message
 * @property string|null $comment
 * @property int $created_at
 * @property int $updated_at
 */
class Request extends ActiveRecord
{
    const STATUS_ACTIVE = 'Active';
    const STATUS_RESOLVED = 'Resolved';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'message'], 'required'],
            [['message', 'comment'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'email'], 'string', 'max' => 100],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_RESOLVED]],
            ['email', 'email'],
            
            // Комментарий обязателен при статусе Resolved
            ['comment', 'required', 'when' => function($model) {
                return $model->status == self::STATUS_RESOLVED;
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'status' => 'Status',
            'message' => 'Message',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
  
    public function isStatusChangedToResolved()
    {
        return $this->getOldAttribute('status') != self::STATUS_RESOLVED && 
               $this->status == self::STATUS_RESOLVED;
    }
}