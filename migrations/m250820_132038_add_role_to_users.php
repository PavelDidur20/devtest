<?php

use yii\db\Migration;

class m250820_132038_add_role_to_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%users}}', 'role', $this->string(20)->notNull()->defaultValue('client'));
        
        // Обновляем существующих пользователей
        $this->update('{{%users}}', ['role' => 'manager'], ['username' => 'admin']);
        $this->update('{{%users}}', ['role' => 'client'], ['username' => 'demo']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'role');
    
    }
}
