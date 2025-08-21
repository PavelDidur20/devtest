<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request}}`.
 */
class m250820_104313_create_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%requests}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'email' => $this->string(100)->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('Active'),
            'message' => $this->text()->notNull(),
            'comment' => $this->text()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
        ]);


            $this->createIndex('idx-request-status', '{{%request}}', 'status');
            $this->createIndex('idx-request-email', '{{%request}}', 'email');
            $this->createIndex('idx-request-created_at', '{{%request}}', 'created_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%request}}');
    }
}
