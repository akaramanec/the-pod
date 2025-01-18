<?php

namespace backend\modules\notification\models\service;

use backend\modules\customer\models\Customer;
use backend\modules\notification\exception\NotificationDbException;
use backend\modules\notification\models\db\BotNotificationCustomer;
use backend\modules\notification\models\db\BotNotificationSetting;
use \backend\modules\notification\models\db\BotNotification;
use backend\modules\notification\models\enum\NotificationStatusEnum;
use backend\modules\notification\models\form\NotificationForm;
use backend\modules\notification\models\helpers\NotificationHelpers;
use backend\modules\notification\models\query\BotNotificationQuery;
use backend\modules\notification\models\query\BotNotificationSettingQuery;
use backend\modules\shop\models\Order;
use Mpdf\Tag\P;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "bot_notification".
 *
 * @property int $id
 * @property string $name
 * @property string $text
 * @property string|null $img
 * @property int|null $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BotNotificationSetting[] $botNotificationSettings
 */
class NotificationService extends BotNotification
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%bot_notification}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'text' => 'Текст',
            'img' => 'Img',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param int $id
     * @return NotificationForm
     * @throws NotificationDbException
     */
    public static function makeForm(int $id): NotificationForm
    {
        $model = self::findOne($id);
        if (!empty($model)) {
            $form = new NotificationForm();
            $form->id = $model->id;
            $form->name = $model->name;
            $form->text = $model->text;
            $form->img = $model->img;
            if (isset($model->botNotificationSettings)) {
                foreach ($model->botNotificationSettings as $setting) {
                    $form->settings[$setting->type] = $setting->value;
                }
            }

            return $form;

        } else {
            throw new NotificationDbException(sprintf("Not found Notification by Id: %d", $id));
        }
    }

    /**
     * @param NotificationForm $form
     * @return bool
     */
    public static function saveByForm(NotificationForm $form): bool
    {
        $modelNotification = $form->id ? self::findOne($form->id) : new self;
        $modelNotification->name = $form->name;
        $modelNotification->text = $form->text;
        $modelNotification->img = $form->img;

        if (!empty($form->settings)) {
            $modelNotification->status = NotificationStatusEnum::ACTIVE;
            if ($modelNotification->save()) {
                $form->id = $modelNotification->id;
                foreach ($form->settings as $type => $value) {
                    $setting = BotNotificationSetting::findOne(['notification_id' => $modelNotification->id, 'type' => $type])
                        ?? new BotNotificationSetting();
                    $setting->notification_id = $modelNotification->id;
                    $setting->type = $type;
                    $setting->value = $value;
                    $setting->save();
                }
            }
        }

        return true;
    }


    /**
     * @param int $type
     * @return string|null
     */
    public function getSettingByType(int $type)
    {
        foreach ($this->botNotificationSettings as $setting) {
            if ($setting->type === $type)
                return $setting->value;
        }

        return null;
    }

    /**
     * @param int $settingType
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCustomersNoSend(int $settingType)
    {
        $settingValue = $this->getSettingByType($settingType);
        $createdAt = NotificationHelpers::getCreatedTimeByTimeSetting($settingValue);
        $orderNew = Order::STATUS_NEW;
        $customers = Customer::find()->alias('bc')
            ->leftJoin('shop_order as so', 'bc.id = so.customer_id')
            ->leftJoin('bot_notification_customer as bnc', 'bc.id = bnc.customer_id')
            ->leftJoin('bot_notification as bn', 'bn.id = bnc.notification_id')
            ->where(['and',
                ["<=", 'bc.created_at', $createdAt],
                ['bc.status' => [Customer::STATUS_ACTIVE, Customer::STATUS_SUBSCRIBED]],
                ["=", "(SELECT count(bnc.notification_id) FROM bot_notification_customer as bnc WHERE bnc.notification_id = $this->id AND bnc.customer_id = bc.id)", 0],
                ['=', "(SELECT count(shop_order.id) FROM shop_order WHERE shop_order.customer_id = bc.id AND shop_order.status != $orderNew)", 0]
            ])->all();

        return $customers;
    }

    /**
     * @param int $botCustomerId
     */
    public function setSendCustomer(int $botCustomerId)
    {
        $modelNotificationCustomer = new BotNotificationCustomer();
        $modelNotificationCustomer->customer_id = $botCustomerId;
        $modelNotificationCustomer->notification_id = $this->id;
        $modelNotificationCustomer->save();
    }

}
