<?php

use yii\db\Migration;

/**
 * Class m210709_130423_alter_table_order_add_column_source
 */
class m210709_130423_alter_table_order_add_column_source extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%shop_order}}',
            'source',
            $this->integer()->defaultValue(null)->after('status')
        );

        $this->createIndex('idx-shop_order-source', '{{%shop_order}}', 'source');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%shop_order}}', 'source');
    }

}
