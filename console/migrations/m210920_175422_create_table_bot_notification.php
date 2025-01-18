<?php

use yii\db\Expression;
use yii\db\Migration;


/**
 * Class m210920_175422_create_table_bot_notification
 */
class m210920_175422_create_table_bot_notification extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable("{{%bot_notification}}", [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->unique(),
            'text' => $this->text()->notNull(),
            'img' => $this->string(),
            'status' => $this->integer(10)->defaultValue(0),
            'created_at' => $this->dateTime()->notNull()->defaultValue(new Expression('NOW()')),
            'updated_at' => $this->dateTime()->notNull()->defaultValue(new Expression('NOW()')),
        ]);

        $this->createIndex('idx-bot_notification-name', '{{%bot_notification}}', 'name', true);
        $this->createIndex('idx-bot_notification-status', '{{%bot_notification}}', 'status');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bot_notification}}');
    }

}
