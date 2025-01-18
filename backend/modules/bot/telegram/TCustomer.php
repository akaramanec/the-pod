<?php

namespace backend\modules\bot\telegram;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotSession;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\CustomerTag;
use backend\modules\customer\models\form\CustomerForm;
use backend\modules\customer\models\LinkBot;
use backend\modules\customer\models\LinkBotLink;
use backend\modules\lesson\models\Courses;
use Yii;

class TCustomer
{

    public static function getOrSetByPlatformId()
    {
        if (!Yii::$app->tm->platformId) {
            exit(__METHOD__);
        }
        $customer = CustomerForm::find()->where(['platform_id' => Yii::$app->tm->platformId])->limit(1)->one();
        if ($customer && $customer->status == Customer::STATUS_BLACKLIST) {
            exit(__METHOD__);
        }
        if ($customer) {
            return $customer;
        }
        $customer = new CustomerForm();
        $customer->platform_id = (string)Yii::$app->tm->platformId;
        $customer->bot_id = Yii::$app->tm->bot->id;
        $username = '';
        if (isset(Yii::$app->tm->input->message->chat->username)) {
            $username .= ' ' . Yii::$app->tm->input->message->chat->username;
        }
        if (isset(Yii::$app->tm->input->message->chat->last_name)) {
            $username .= ' ' . Yii::$app->tm->input->message->chat->last_name;
        }
        if (isset(Yii::$app->tm->input->message->chat->first_name)) {
            $username .= ' ' . Yii::$app->tm->input->message->chat->first_name;
        }
        if (isset(Yii::$app->tm->input->message->chat->phone)) {
            $customer->phone = Yii::$app->tm->input->message->chat->phone;
        }
        if (isset(Yii::$app->tm->input->message->chat->email)) {
            $customer->email = Yii::$app->tm->input->message->chat->email;
        }
        $customer->username = $username;
        $customer->status = Customer::STATUS_NEW;
        $customer->tags = [CustomerTag::TAG_NEW];
        if ($customer->save()) {
            self::saveReferral($customer);
            TBaseCommon::getUserProfilePhotos($customer);
            return $customer;
        } else {
            $bot = new TBaseCommon();
            $bot->errors($customer->errors);
        }
    }

    public static function saveReferral($customer)
    {
        if (!isset(Yii::$app->tm->input->message->text)) {
            return;
        }
        $text = explode(' ', Yii::$app->tm->input->message->text);
        if (count($text) >= 2 && $text[0] == '/start') {
            $e = explode('_', $text[1]);

            if (isset($e[0])) {
                $customerIdParent = $e[0];
                if ($customerIdParent && $customer->id != $customerIdParent) {
                    $parent = Customer::findOne($customerIdParent);
                    if ($parent && $parent->parent_id != $customer->id) {
                        $customer->parent_id = $customerIdParent;
                        $customer->save();
                    }
                }
                foreach (LinkBot::find()->all() as $linkBot) {
                    if ($customerIdParent == $linkBot->name) {
                        $lbl = new LinkBotLink();
                        $lbl->link_bot_id = $linkBot->id;
                        $lbl->customer_id = $customer->id;
                        if ($lbl->save()) {
                            break;
                        }
                    }
                }
            }

            if (isset($e[1])) {
                $session = new BotSession();
                $session->setPlatform = Bot::TELEGRAM;
                $session->customerId = $customer->id;
                $session->set('referralInput', ['productCode' => $e[1]]);
            }
        }
    }
}
