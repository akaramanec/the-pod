<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_order_status_tracking}}`.
 */
class m210811_110200_create_shop_order_status_tracking_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_order_status_tracking}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(10)->unsigned(),
            'manager_id' => $this->integer(10)->unsigned(),
            'old_status' => $this->tinyInteger(),
            'new_status' => $this->tinyInteger(),
            'step_time' => $this->integer(),
            'created_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-shop_order_status_tracking-order_id',
            '{{%shop_order_status_tracking}}',
            'order_id',
            '{{%shop_order}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-shop_order_status_tracking-manager_id',
            '{{%shop_order_status_tracking}}',
            'manager_id',
            '{{%auth_admin}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-shop_order_status_tracking-order_id',
            '{{%shop_order_status_tracking}}'
        );

        $this->dropForeignKey(
            'fk-shop_order_status_tracking-manager_id',
            '{{%shop_order_status_tracking}}'
        );

        $this->dropTable('{{%shop_order_status_tracking}}');
    }
}
