<?php

namespace console\controllers;

use backend\modules\admin\models\AuthLogger;
use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\telegram\TBaseCommon;
use backend\modules\bot\viber\VAdmin;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\CustomerTag;
use backend\modules\customer\models\CustomerTagLink;
use src\helpers\Date;
use src\helpers\Demo;
use Yii;
use yii\console\Controller;

class ServiceController extends Controller
{

    public function actionDelMessage()
    {
        $pattern = '/^.Шо ты, Мирон/';
        $bot = new TBaseCommon();
        foreach (BotLogger::find()->all() as $log) {
            if (isset($log->data['ok']) == true &&
                isset($log->data['result']['text']) &&
                preg_match($pattern, $log->data['result']['text']) &&
                isset($log->data['result']['message_id']) &&
                isset($log->data['result']['chat']['id'])) {
                try {
                    $bot->deleteMessageByPlatformIdAndMessageId($log->data['result']['chat']['id'], $log->data['result']['message_id']);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }

    public function actionTag()
    {
        foreach (Customer::find()->each() as $customer) {
            try {

                $model = new CustomerTagLink();
                $model->customer_id = $customer->id;
                $model->tag_id = CustomerTag::TAG_NEW;
                $model->save();
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    public function actionTest()
    {
        new Demo();
    }

    public function actionClearLogger()
    {
        $dayMinus = Date::minusPlus(Date::datetime_now(), '-4 day');
        AuthLogger::deleteAll(['<', 'created_at', $dayMinus]);
        BotLogger::deleteAll(['<', 'created_at', $dayMinus]);

        BotLogger::save_input('ok', __METHOD__);
    }

    public function actionSendMessage()
    {
        $text = "На связи The Pod 🙋🏻‍♂️

Мы увидели, что вы перешли в наш бот и не делали заказ 😢

В связи с этим мы предлагаем вам ознакомится с функционалом нашего бота. У нас большой выбор, быстрая доставка и круглосуточная поддержка с менеджером ✅

Так же приглашаем в гости в наше заведение по адресу бульвар Леси Украинки 23 (м.Печерская). У нас можно бесплатно попробовать все вкусы и выбрать, который Вам понравится 💪🏻

Сделайте заказ после того, как получили это сообщение и получите скидку -10% навсегда 😌❤️";
        $botCustomers = Customer::find()->where(['between', 'created_at', "2021-09-16 10:30:00", "2021-09-16 13:00:00"])->all();

        $countCustomers = count($botCustomers);

        foreach ($botCustomers as $customer) {
            print_r(($countCustomers--)."...");
            try {
                if ($customer->bot->platform == Bot::TELEGRAM) {
                    Yii::$app->tm->customer = $customer;
                    Yii::$app->tm->platformId = $customer->platform_id;
                    $TAdmin = new TAdmin();

                    $TAdmin->keyboard($text, $TAdmin->keyboardMainMenu());

                }
                if ($customer->bot->platform == Bot::VIBER) {
                    Yii::$app->vb->customer = $customer;
                    Yii::$app->vb->platformId = $customer->platform_id;
                    $VAdmin = new VAdmin();

                    $VAdmin->keyboard($text, $VAdmin->mainMenuKeyboard());

                }
            } catch (\Exception $e) {
                print_r("Пользователю id: $customer->id не удалось отправить сообщение");
            }
        }

    }
}
