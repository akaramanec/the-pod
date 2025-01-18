<?php

namespace console\controllers;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\telegram\TCommon;
use backend\modules\bot\viber\VAdmin;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\Newsletter;
use backend\modules\customer\models\NewsletterMessages;
use backend\modules\notification\models\db\BotNotificationCustomer;
use backend\modules\notification\models\enum\NotificationSettingEnum;
use backend\modules\notification\models\enum\NotificationStatusEnum;
use backend\modules\notification\models\service\NotificationService;
use backend\modules\shop\models\Notice;
use backend\modules\shop\models\NoticeMoveLink;
use backend\modules\shop\models\Order;
use src\helpers\Date;
use Yii;
use yii\console\Controller;


class NotificationController extends Controller
{

    public function actionSendByType($settingType = NotificationSettingEnum::NOT_ACTIVE_TIME)
    {
        /** @var NotificationService[] $notifications */
        $notifications = NotificationService::find()
            ->alias('ns')
            ->joinWith('botNotificationSettings as bns')
            ->where([
                'ns.status' => NotificationStatusEnum::ACTIVE,
                'bns.type' => $settingType
            ])->all();

        NotificationService::updateAll(['status' => NotificationStatusEnum::IN_WORK],
            ['in', 'id', array_column($notifications, 'id')]
        );

        foreach ($notifications as $notification) {
            try {
                /** @var Customer[] $botCustomers */
                $botCustomers = $notification->getCustomersNoSend($settingType);
                if (!empty($botCustomers)) {
                    foreach ($botCustomers as $customer) {
                        if ($this->sendMessage($customer, $notification->text)) {
                            $notification->setSendCustomer($customer->id);
                        }
                    }
                }
            } catch (\Exception $e) {
                BotLogger::save_input($e->getTraceAsString(), 'notification');
            }

        }

        NotificationService::updateAll(['status' => NotificationStatusEnum::ACTIVE],
            ['in', 'id', array_column($notifications, 'id')]
        );

    }


    /**
     * @param Customer $customer
     * @param string $message
     * @return bool
     */
    private function sendMessage(Customer $customer, string $message): bool
    {
        $result = false;
        try {
            if ($customer->bot->platform == Bot::TELEGRAM) {
                Yii::$app->tm->customer = $customer;
                Yii::$app->tm->platformId = $customer->platform_id;
                $TAdmin = new TAdmin();
                $response = $TAdmin->keyboard($message, $TAdmin->keyboardMainMenu());
                if ($response['ok'] == true) {
                    $result = true;
                }
            }
            if ($customer->bot->platform->bot->platform == Bot::VIBER) {
                Yii::$app->vb->customer = $customer;
                Yii::$app->vb->platformId = $customer->platform_id;
                $VAdmin = new VAdmin();

                $VAdmin->keyboard($message, $VAdmin->mainMenuKeyboard());
                $result = true;
            }

        } catch (\Exception $e) {
            BotLogger::save_input(['status' => 'error', 'message' => $e->getMessage()], __METHOD__);
        }

        return $result;
    }

}
