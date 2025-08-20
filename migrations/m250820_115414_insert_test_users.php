<?php

use yii\db\Migration;

class m250820_115414_insert_test_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
          $time = time();
        
        //Тестовые пользователи
        $this->batchInsert('users', [
            'username', 
            'password_hash', 
            'auth_key', 
            'created_at', 
            'updated_at'
        ], [
            [
                'admin',
                Yii::$app->security->generatePasswordHash('admin'),
                Yii::$app->security->generateRandomString(),
                $time,
                $time
            ],
            [
                'demo',
                Yii::$app->security->generatePasswordHash('demo'),
                Yii::$app->security->generateRandomString(),
                $time,
                $time
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250820_115414_insert_test_users cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250820_115414_insert_test_users cannot be reverted.\n";

        return false;
    }
    */
}
