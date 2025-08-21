<?php
// models/Request.php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Класс модели для таблицы "requests".
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
        return '{{%requests}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {

        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => fn() => date('Y-m-d H:i:s'),
            ],
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
            [['name', 'email'], 'string', 'max' => 100],
            [['created_at', 'updated_at'], 'safe'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_RESOLVED]],
            ['email', 'email'],
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

        public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->isAttributeChanged('status') && $this->status === self::STATUS_RESOLVED) {
            if (trim((string)$this->comment) === '') {
                $this->addError('comment', 'Комментарий обязателен для решённых заявок');
            }
        }

        return true;
    }
}
