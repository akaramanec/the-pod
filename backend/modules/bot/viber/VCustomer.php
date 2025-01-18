<?php

namespace backend\modules\bot\viber;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotSession;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\CustomerTag;
use backend\modules\customer\models\CustomerTagLink;
use backend\modules\customer\models\form\CustomerForm;
use backend\modules\customer\models\LinkBot;
use backend\modules\customer\models\LinkBotLink;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;

class VCustomer
{
    public static function getOrSetByPlatformId()
    {
        if (Yii::$app->vb->platformId) {
            $customer = CustomerForm::find()->where(['platform_id' => Yii::$app->vb->platformId])->limit(1)->one();
            if ($customer && $customer->status == Customer::STATUS_BLACKLIST) {
                exit('VCustomer getOrSetByPlatformId');
            }
            if ($customer === null) {
                $img = null;
                if (Yii::$app->vb->type == 'open') {
                    if (isset(Yii::$app->vb->input->user->avatar)) {
                        $img = Yii::$app->vb->input->user->avatar;
                    }
                }
                if (Yii::$app->vb->type == 'text') {
                    if (isset(Yii::$app->vb->input->sender->avatar)) {
                        $img = Yii::$app->vb->input->sender->avatar;
                    }
                }
                $customer = new CustomerForm();
                $customer->platform_id = (string)Yii::$app->vb->platformId;
                $customer->bot_id = Yii::$app->vb->bot->id;
                $customer->status = Customer::STATUS_NEW;
                $customer->tags = [CustomerTag::TAG_NEW];

                if ($customer->save() && $img) {
                    self::saveReferral($customer);
                    if ($img) {
                        $customer->img = self::uploadPhoto($img, $customer);
                        $customer->save(false);
                    }
                }
            }
            return $customer;
        }
    }

    public static function saveReferral($customer)
    {
        if (Yii::$app->vb->type == 'open' && isset(Yii::$app->vb->input->context)) {
            $context = explode('_', Yii::$app->vb->input->context);
            if (isset($context[0]) && $context[0] == 'ref' && $customer->id != $context[1]) {
                $parent = Customer::findOne($context[1]);
                if ($parent && $parent->parent_id != $customer->id) {
                    $customer->parent_id = $context[1];
                    $customer->save();
                }
                if (isset($context[2])) {
                    $session = new BotSession();
                    $session->setPlatform = Bot::VIBER;
                    $session->customerId = $customer->id;
                    $session->set('referralInput', ['productCode' => $context[2]]);
                }
            }
            if (isset($context[0]) && $context[0] == 'linkBot') {
                foreach (LinkBot::find()->all() as $linkBot) {
                    if ($context[1] == $linkBot->name) {
                        $lbl = new LinkBotLink();
                        $lbl->link_bot_id = $linkBot->id;
                        $lbl->customer_id = $customer->id;
                        return $lbl->save();
                    }
                }
            }
        }
    }

    public static function uploadPhoto($url, $model)
    {
        parse_str($url, $parseQuery);
        $extension = '.' . ArrayHelper::getValue($parseQuery, 'fltp');
        $path = Yii::$app->params['imgPath'] . '/customer/' . $model->id;
        $fileName = 'avatar' . $extension;
        $fullPath = $path . '/' . $fileName;
        BaseFileHelper::createDirectory($path, 0777);
        file_put_contents($fullPath, file_get_contents($url));
        @chmod($fullPath, 0777);
        return $extension;
    }
}
