<?php

use backend\modules\bot\models\BotCommand;
use yii\db\Migration;

/**
 * Class m211110_173604_add_bot_text_referral_v_2
 */
class m211110_173604_add_bot_text_referral_v_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->del();

        $this->batchInsert('{{%bot_command}}', ['name', 'description', 'status'], $this->batchParams());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->del();
    }

    private function del(): bool
    {
        $this->delete('{{%bot_command}}', ['name' => array_map(function ($item) {
            return $item[0];
        }, $this->batchParams())]);
        return true;
    }

    private function batchParams(): array
    {
        $texts = [
            ['messageMyStatistics', "Всего подписчиков: {{customersCount}}\n"
                . "Подписчиков 1-го уровня: {{customersLevel1}}\n"
                . "Подписчиков 2-го уровня: {{customersLevel2}}\n"
                . "Количество заказов: {{ordersCount}}\n"],
            ['messageMyBonus', "Начислено: {{sumTotalOrders}} грн\n"
                . "Выведено: {{sumTotalPayed}} грн\n"
                . "Остаток: {{sumTotalDebit}} грн\n"
            ],
            ['messageNoWithdrawalMoney', "Средств недостаточно для вывода.\n"
                . " Минимальная сумма вывода {{minWithdrawSum}} грн\n"
                . " Вывод возможен через {{lefSumUntilWithdrawal}} грн"
            ]
        ];

        return array_map(function ($item) {
            array_push($item, BotCommand::STATUS_VIEW);
            return $item;
        }, $texts);
    }
}
