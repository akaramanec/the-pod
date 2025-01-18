<?php

use yii\db\Migration;

/**
 * Class m210920_190542_create_table_bot_notification_settings
 */
class m210920_190542_create_table_bot_notification_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bot_notification_setting}}', [
            'notification_id' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),
            'value' => $this->string()->notNull()
        ]);

        $this->createIndex('id-bot_notification_setting-notification_id', '{{%bot_notification_setting}}', 'notification_id');
        $this->createIndex('id-bot_notification_setting-type', '{{%bot_notification_setting}}', 'type');
        $this->createIndex('id-bot_notification_setting-notification_id-type', '{{%bot_notification_setting}}', ['notification_id', 'type']);
        $this->addPrimaryKey('pk-bot_notification_setting', '{{%bot_notification_setting}}', ['notification_id', 'type']);
        $this->addForeignKey('fk-bot_notification_setting-notification_id',
            '{{%bot_notification_setting}}',
            'notification_id',
            '{{%bot_notification}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bot_notification_setting}}');
    }

}
