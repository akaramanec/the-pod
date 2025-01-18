<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%shop_order}}`.
 */
class m210721_131911_add_success_at_column_to_shop_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%shop_order}}', '{{%success_at}}', $this->timestamp()->after('{{%updated_at}}')->null());

        $this->createIndex(
            '{{%idx-shop_order-success_at}}',
            '{{%shop_order}}',
            '{{%success_at}}'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            '{{%idx-shop_order-success_at}}',
            '{{%shop_order}}'
        );

        $this->dropColumn('{{%shop_order}}', '{{%success_at}}');
    }
}
