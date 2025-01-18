<?php

use yii\db\Migration;

/**
 * Class m211104_123515_alter_table_order_add_column_blogger_bonus
 */
class m211104_123515_alter_table_order_add_column_blogger_bonus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%shop_order}}', 'blogger_bonus', $this->decimal(10, 2));

        $this->createIndex('idx-shop_order-blogger_bonus', '{{%shop_order}}', 'blogger_bonus');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%shop_order}}', 'blogger_bonus');
    }

}
