<?php

use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m211018_180222_create_table_blogger_withdrawal_request
 */
class m211018_180222_create_table_blogger_withdrawal_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable("{{%blogger_withdrawal_request}}", [
            'id' => $this->primaryKey(),
            'bot_customer_id' => $this->integer()->notNull()->unsigned(),
            'bot_customer_card_id' => $this->integer(),
            'sum' => $this->float(),
            'status'=> $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull()->defaultValue(new Expression('NOW()')),
            'updated_at' => $this->dateTime()->notNull()->defaultValue(new Expression('NOW()'))
        ], $tableOptions);

        $this->createIndex('idx-blogger_withdrawal_request-bot_customer_id', '{{%blogger_withdrawal_request}}', 'bot_customer_id');
        $this->createIndex('idx-blogger_withdrawal_request-bot_customer_card_id', '{{%blogger_withdrawal_request}}', 'bot_customer_card_id');
        $this->createIndex('idx-blogger_withdrawal_request-sum', '{{%blogger_withdrawal_request}}', 'sum');
        $this->createIndex('idx-blogger_withdrawal_request-status', '{{%blogger_withdrawal_request}}', 'status');

        $this->addForeignKey('fk-blogger_withdrawal_request-bot_customer_id',
            '{{%blogger_withdrawal_request}}',
            '{{%bot_customer_id}}',
            'bot_customer',
            'id',
            'CASCADE'
        );

        $this->addForeignKey('fk-blogger_withdrawal_request-bot_customer_card_id',
            '{{%blogger_withdrawal_request}}',
            '{{%bot_customer_card_id}}',
            'bot_customer_card',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%blogger_withdrawal_request}}");
    }
}
