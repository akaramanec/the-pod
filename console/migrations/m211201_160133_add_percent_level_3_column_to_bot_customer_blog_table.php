<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%bot_customer_blog}}`.
 */
class m211201_160133_add_percent_level_3_column_to_bot_customer_blog_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bot_customer_blog', 'percent_level_3', $this->tinyInteger()->defaultValue(1)->after('percent_level_2'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
