<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_poll}}`.
 */
class m210721_113338_create_shop_poll_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_poll}}', [
            '{{%id}}' => $this->primaryKey(),
            '{{%name}}' => $this->string()->unique(),
            '{{%question}}' => $this->text(),
            '{{%first_send_after}}' => $this->string(10),
            '{{%second_send_after}}' => $this->string(10),
            '{{%status}}' => $this->tinyInteger()->defaultValue(3),
            '{{%img}}' => $this->string(),
            '{{%updated_by}}' => $this->integer(),
            '{{%created_at}}' => $this->timestamp()->null(),
            '{{%updated_at}}' => $this->timestamp()->null(),
        ]);

        $this->createIndex(
            '{{%idx-shop_poll-updated_by}}',
            '{{%shop_poll}}',
            '{{%updated_by}}'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            '{{%idx-shop_poll-updated_by}}',
            '{{%shop_poll}}'
        );

        $this->dropTable('{{%shop_poll}}');
    }
}
