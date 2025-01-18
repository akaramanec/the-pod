<?php

namespace console\controllers;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\telegram\TCommon;
use backend\modules\bot\viber\VAdmin;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Notice;
use backend\modules\shop\models\NoticeMoveLink;
use backend\modules\shop\models\Order;
use src\helpers\Date;
use Yii;
use yii\console\Controller;


class NoticeController extends Controller
{

    public function actionIndex()
    {
        $notices = Notice::find()
            ->where(['status' => Notice::STATUS_ACTIVE])
            ->orderBy('idle_time asc')->all();
        if ($notices === null) {
            BotLogger::save_input([
                'message' => 'notices null',
            ]);
            return null;
        }

        foreach ($notices as $notice) {
            $dayTo = Date::minusPlus(Date::datetime_now(), "-$notice->idle_time day");
            $dayFrom = Date::minusPlus($dayTo, "-1 day");
            $orders = Order::find()
                ->where(['status' => Order::STATUS_CLOSE_SUCCESS])
                ->andWhere(['between', 'updated_at', $dayFrom, $dayTo])
                ->andWhere(['!=', 'customer_id', Customer::CUSTOMER_SITE])
                ->with(['botCustomer.bot']);
            foreach ($orders->each() as $order) {
                if ($order->customer->platform_id == Yii::$app->params['groupTmChatId']) {
                    continue;
                }
                $nol = NoticeMoveLink::findOne(['order_id' => $order->id, 'notice_id' => $notice->id]);
                if ($nol === null) {
                    if ($order->customer->bot->platform == Bot::TELEGRAM) {
                        Yii::$app->tm->customer = $order->customer;
                        Yii::$app->tm->platformId = $order->customer->platform_id;
                        $admin = new TAdmin();
                        $admin->notificationAfterSuccessfulOrder($notice, $order);
                    }
                    if ($order->customer->bot->platform == Bot::VIBER) {
                        Yii::$app->vb->customer = $order->customer;
                        Yii::$app->vb->platformId = $order->customer->platform_id;
                        $admin = new VAdmin();
                        $admin->notificationAfterSuccessfulOrder($notice, $order);
                    }
                }
            }
        }

        BotLogger::save_input(['status' => 'ok'], __METHOD__);
    }

    public function actionNewVersion()
    {
        $customer = Customer::find()
            ->alias('customer')
            ->where(['customer.status' => Customer::STATUS_ACTIVE])
            ->andWhere(['bot.platform' => Bot::TELEGRAM])
            ->joinWith(['bot AS bot']);
        $x = 1;
        foreach ($customer->each(35) as $customer) {
            try {
                Yii::$app->tm->platformId = $customer->platform_id;
                Yii::$app->tm->customer = $customer;
                $common = new TCommon();
                if ($common->auth()) {
                    $common->start();
                    if ($x == 35) {
                        $x = 1;
                        sleep(1);
                    }
                    $x++;
                }
            } catch (\Exception $e) {
                BotLogger::save_input([
                    'status' => 'ok',
                    'message' => $e->getMessage()
                ], __METHOD__);
                continue;
            }
        }
        BotLogger::save_input(['status' => 'ok'], __METHOD__);
    }
}
