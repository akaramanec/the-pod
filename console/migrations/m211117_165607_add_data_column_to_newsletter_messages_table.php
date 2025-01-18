<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%newsletter_messages}}`.
 */
class m211117_165607_add_data_column_to_newsletter_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('newsletter_messages', 'data', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('newsletter_messages', 'data');
    }
}