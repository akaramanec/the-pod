<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m210716_141535_shop_customer_search
 */
class m210716_141535_shop_customer_search extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-shop_customer_search-customer_id',
            'shop_customer_search'
        );

        $this->dropTable('shop_customer_search');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210716_141535_shop_customer_search cannot be reverted.\n";

        return false;
    }
    */
}
