<?php

namespace backend\controllers;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\viber\VAdmin;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\Newsletter;
use Yii;

class CronController extends \yii\web\Controller
{

    public function actionNewsletters()
    {
        $newsletter = Newsletter::find()
            ->where(['status' => Newsletter::STATUS_SEND])
            ->andWhere(['<=', 'date_departure', Yii::$app->common->datetimeNow])
            ->orderBy('created_at asc')
            ->limit(1)->one();
        if ($newsletter === null) {
            return;
        }
        $newsletter->status = Newsletter::STATUS_IN_WORK;
        $newsletter->save(false);

        $newsletter->setSetting();
        if ($newsletter->customerId) {
            $q = Customer::find()
                ->where(['id' => $newsletter->customerId])
                ->with(['bot']);
            foreach ($q->batch(30) as $customers) {
                foreach ($customers as $customer) {
                    try {
                        if ($customer->bot->platform == Bot::TELEGRAM) {
                            Yii::$app->tm->customer = $customer;
                            Yii::$app->tm->platformId = $customer->platform_id;
                            $TAdmin = new TAdmin();
                            $TAdmin->newsletter($newsletter);
                        }
                        if ($customer->bot->platform == Bot::VIBER) {
                            Yii::$app->vb->customer = $customer;
                            Yii::$app->vb->platformId = $customer->platform_id;
                            $VAdmin = new VAdmin();
                            $VAdmin->newsletter($newsletter);
                        }
                    } catch (\Exception $e) {
                        BotLogger::save_input(['status' => 'error', 'message' => $e->getMessage()], __METHOD__);
                        continue;
                    }
                }
                sleep(1);
            }
        }
        $newsletter->status = Newsletter::STATUS_SENT;
        $newsletter->save(false);
        BotLogger::save_input(['status' => 'ok'], __METHOD__);
    }
}