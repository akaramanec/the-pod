<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%shop_customer_search}}`.
 */
class m210719_124104_drop_shop_customer_search_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex(
            'idx-shop_customer_search-customer_id',
            'shop_customer_search'
        );

        $this->dropTable('{{%shop_customer_search}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable('shop_customer_search', [
            'customer_id' => $this->primaryKey(10),
            'search' => $this->string()
        ]);

        $this->createIndex(
            'idx-shop_customer_search-customer_id',
            'shop_customer_search',
            'customer_id'
        );
    }
}
