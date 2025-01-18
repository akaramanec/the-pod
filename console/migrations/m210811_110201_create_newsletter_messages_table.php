<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%newsletter_messages}}`.
 */
class m210811_110201_create_newsletter_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%newsletter_messages}}', [
            'id' => $this->primaryKey(),
            'newsletter_id' => $this->integer(),
            'customer_id' => $this->integer()->unsigned(),
            'status' => $this->tinyInteger()
        ]);

        $this->addForeignKey(
            'fk-newsletter_messages-newsletter_id',
            '{{%newsletter_messages}}',
            'newsletter_id',
            '{{%newsletter}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-newsletter_messages-customer_id',
            '{{%newsletter_messages}}',
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
        $this->dropTable('{{%newsletter_messages}}');
    }
}
