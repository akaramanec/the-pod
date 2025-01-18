<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%shop_order}}`.
 */
class m210720_173150_add_manager_column_to_shop_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('shop_order', 'manager_id', $this->integer()->after('status')->null());

        $this->createIndex(
            'idx-shop_order-manager_id',
            'shop_order',
            'manager_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-shop_order-manager_id',
            'shop_order'
        );

        $this->dropColumn('shop_order', 'manager_id');
    }
}
