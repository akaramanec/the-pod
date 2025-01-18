<?php

use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m211018_180111_create_table_bot_customer_card
 */
class m211018_180111_create_table_bot_customer_card extends Migration
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

        $this->createTable("{{%bot_customer_card}}", [
            'id' => $this->primaryKey(),
            'bot_customer_id' => $this->integer()->notNull()->unsigned(),
            'number' => $this->string(20)->notNull(),
            'status' => $this->integer(10)->defaultValue(0),
            'created_at' => $this->dateTime()->notNull()->defaultValue(new Expression('NOW()')),
            'updated_at' => $this->dateTime()->notNull()->defaultValue(new Expression('NOW()'))
        ], $tableOptions);

        $this->createIndex('idx-bot_customer_card-bot_customer_id', '{{%bot_customer_card}}', 'bot_customer_id');
        $this->createIndex('idx-bot_customer_card-number', '{{%bot_customer_card}}', 'number');

        $this->addForeignKey('fk-bot_customer_card-bot_customer_id',
            '{{%bot_customer_card}}',
            '{{%bot_customer_id}}',
            'bot_customer',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%bot_customer_card}}");
    }

}
