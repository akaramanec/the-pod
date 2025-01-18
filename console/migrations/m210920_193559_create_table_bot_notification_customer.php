<?php

use yii\db\Migration;

/**
 * Class m210920_193559_create_table_bot_notification_customer
 */
class m210920_193559_create_table_bot_notification_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bot_notification_customer}}', [
            'notification_id' => $this->integer()->notNull(),
            'customer_id' => $this->integer()->unsigned()->notNull()
        ]);

        $this->createIndex('id-bot_notification_customer-notification_id', '{{%bot_notification_customer}}', 'notification_id');
        $this->createIndex('id-bot_notification_customer-customer_id', '{{%bot_notification_customer}}', 'customer_id');
        $this->createIndex('id-bot_notification_customer-notification_id-customer_id', '{{%bot_notification_customer}}', ['notification_id', 'customer_id']);

        $this->addPrimaryKey('pk-bot_notification_customer', '{{%bot_notification_customer}}', ['notification_id', 'customer_id']);

        $this->addForeignKey('fk-bot_notification_customer-notification_id',
            '{{%bot_notification_customer}}',
            'notification_id',
            '{{%bot_notification}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey('fk-bot_notification_customer-customer_id',
            '{{%bot_notification_customer}}',
            'customer_id',
            '{{%bot_customer}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bot_notification_customer}}');
    }

}
