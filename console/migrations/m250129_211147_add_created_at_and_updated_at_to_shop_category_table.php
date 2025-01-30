<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%newsletter}}`.
 */
class m250129_211147_add_created_at_and_updated_at_to_shop_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%shop_category}}', 'created_at', $this->dateTime()->defaultExpression('NOW()'));
        $this->addColumn('{{%shop_category}}', 'updated_at', $this->dateTime()->defaultExpression('NOW()'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%shop_category}}', 'created_at');
        $this->dropColumn('{{%shop_category}}', 'updated_at');
    }
}
