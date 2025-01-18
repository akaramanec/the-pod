<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%bot_customer}}`.
 */
class m211125_212432_add_black_list_and_regular_customer_columns_to_bot_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bot_customer', 'black_list', $this->boolean()->after('blogger')->defaultValue(0));
        $this->addColumn('bot_customer', 'regular_customer', $this->boolean()->after('black_list')->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
