<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%newsletter}}`.
 */
class m220207_114147_add_video_column_to_newsletter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('newsletter', 'video', $this->string()->after('img'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('newsletter', 'video');
    }
}
