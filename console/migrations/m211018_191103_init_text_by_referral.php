<?php

use backend\modules\bot\models\BotCommand;
use yii\db\Migration;

/**
 * Class m211018_191103_init_text_by_referral
 */
class m211018_191103_init_text_by_referral extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('{{%bot_command}}', ['name', 'description', 'status'], $this->batchParams());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%bot_command}}', ['name' => array_map(function ($item) {
            return $item[0];
        }, $this->batchParams())]);
    }

    private function batchParams(): array
    {
        $texts = [
            ['buttonSmokeFree', 'Курить бесплатно'],
            ['buttonMyBonus', 'Мои начисления'],
            ['buttonCopyLink', '🔗 Копировать ссылку'],
            ['buttonWant', 'Хочу'],
            ['buttonStatistics', '📈 Статистика'],
            ['buttonReferralInfo', '📖 Как это работает?'],
            ['buttonGetMoney', '💳 Вывод средств'],
            ['buttonWithdrawAll', 'Вывести все'],
            ['buttonWithdrawPart', 'Указать сумму'],
            ['buttonByPod', '😉 Купить сигарету'],
            ['buttonApprove', 'Все верно'],
            ['buttonEditCard', 'Редактировать карту'],
            ['buttonAddCard', '+ Добавить карту'],
            ['buttonEditSum', '« Редактировать сумму'],
            ['paymentByBloggerBonus','Списать начисления'],
            ['buttonConfirm', '✅ Подтвердить'],
            ['buttonNotConfirm', '❌  Отмена'],
//            ['button', ''],
            ['messageSelectReferralDivision', 'Выберите раздел, который хотите посмотреть'],
            ['messageCreatedReferral', "<b>Теперь у Вас есть реферальная ссылка</b> 👍"
                . "\n\n"
                . "Немного о том, как это работает :\n"
                . "<b>Уровень 1:</b> Приклашая друзей, Вы получаете по 10% начислений с каждого их заказа 👍"
                . "\n"
                . "<b>Уровень 2:</b> Если Ваши подписчики пригласят своих друзей - Вы будете получать по 5% начислений от 
стоимости их заказа 😉"
                . "\n"
                . "<b>Уровень 3:</b> Получайте 1% от заказов подписчиков Ваших подписчиков 2-го уровня 😎"
                . "\n\n"
                . "Используйте кнопку “кабинет”, что бы отслеживать свои начисления и выводить деньги 👇"
            ],
            ['messageReferral', "<b>Реферальная ссылка</b> \n\n {{referral_link}}"],
            ['messageSmokeFree', "<b>Курить бесплатно может каждый!</b>\n"
                . "Приведите 10 друзей и получайте реальные деньги от их заказов 😎"],
            ['messageMyBonus', "Ваши подписчики: {{customersCount}}\n"
                . "Заказов: {{ordersCount}}\n"
                . "Выведено: {{sumTotalPayed}} грн\n"
                . "Начислено: {{sumTotalOrders}} грн\n"
            ],
            ['messageMyBonusNo', "<b>У Вас пока нет подписчиков</b> 🙁"
                . "\n\n"
                . "Передавайте свою реферальную ссылку друзьям, если они еще не пользуются ботом"
            ],
            ['messageReferralInfo', "<b>Теперь у Вас есть реферальная ссылка</b> 👍"
                . "\n"
                . "<b>Уровень 1:</b> Приглашая друзей, Вы получаете по 10% начислений с каждого их заказа 👍"
                . "\n"
                . "<b>Уровень 2:</b> Если Ваши подписчики пригласят своих друзей - Вы будете получать по 5% начислений от 
стоимости их заказа 😉"
                . "\n"
                . "<b>Уровень 3:</b> Получайте 1% от заказов подписчиков Ваших подписчиков 2-го уровня 😎"
                . "\n\n"
                . "Используйте кнопку “кабинет”, что бы отслеживать свои начисления. Вы можете вывести деньги, если у Вас 
более {{minSumExchange}} грн. Для этого перейтите в “начисления”"
            ],
            ['messageGetMoney', "{{sumDebt}} грн доступно к выводу"],
            ['messageEnterWithdrawSum', "Укажите сумму, которую хотите вывести (от {{sumMin}} до {{sumMax}} грн) 👇"],
            ['messageEnterYourCard', "Введите номер карты для перевода средств 👇"],
            ['messageApproveCard', "Проверьте правильность ввода номера карты:\n{{cardNumber}}"],
            ['messageSelectYourCard', "Выберите карту для перевода:"],
            ['messageErrorSumValidate', "Указанная вами сумма недоступна"],
            ['messageErrorCardValidate', "Неверный формат\nВведите карту в формате хххх хххх хххх хххх"],
            ['messageSuccessWithdrawalRequest', "Ваша заявка на вывод средств принята.\nМенеджер свяжется с Вами в ближайшее время"],
            ['messagePayBlogger', "<b>Оплачено: {{payBlogger}}</b>\n<b>Остаток начислений: {{balanceBlogger}}</b>"],
//            ['message', ""],
//            ['message', ""],
//            ['message', ""],
        ];

        return array_map(function ($item) {
            array_push($item, BotCommand::STATUS_VIEW);
            return $item;
        }, $texts);
    }
}
