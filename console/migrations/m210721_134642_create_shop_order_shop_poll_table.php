<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_order_shop_poll}}`.
 */
class m210721_134642_create_shop_order_shop_poll_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_order_shop_poll}}', [
            '{{%id}}' => $this->primaryKey(),
            '{{%order_id}}' => $this->integer(10)->unsigned(),
            '{{%poll_id}}' => $this->integer(11),
            '{{%status}}' => $this->tinyInteger()->defaultValue(0),
            '{{%answer_first}}' =>  $this->tinyInteger(),
            '{{%answer_second}}' =>  $this->tinyInteger(),
            '{{%created_at}}' => $this->dateTime(),
            '{{%updated_at}}' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-shop_order_shop_poll-order_id',
            '{{%shop_order_shop_poll}}',
            'order_id',
            '{{%shop_order}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-shop_order_shop_poll-poll_id',
            '{{%shop_order_shop_poll}}',
            'poll_id',
            '{{%shop_poll}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-shop_order_shop_poll-status',
            '{{%shop_order_shop_poll}}',
            'status'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-shop_order_shop_poll-order_id',
            '{{%shop_order_shop_poll}}'
        );
        $this->dropForeignKey(
            'fk-shop_order_shop_poll-poll_id',
            '{{%shop_order_shop_poll}}'
        );

        $this->dropIndex(
            'idx-shop_order_shop_poll-status',
            '{{%shop_order_shop_poll}}'
        );

        $this->dropTable('{{%shop_order_shop_poll}}');
    }
}
