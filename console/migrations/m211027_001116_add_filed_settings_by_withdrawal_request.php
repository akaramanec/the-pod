<?php

use yii\db\Migration;

/**
 * Class m211027_001116_add_filed_settings_by_withdrawal_request
 */
class m211027_001116_add_filed_settings_by_withdrawal_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingWithdrawalRequest = new \backend\modules\system\models\Setting();
        $settingWithdrawalRequest->name = "Вывод средств";
        $settingWithdrawalRequest->slug = 'withdrawalRequestSetting';
        if ($settingWithdrawalRequest->save()) {
            $settingWithdrawalRequestItem = new \backend\modules\system\models\SettingItem();
            $settingWithdrawalRequestItem->setting_id = $settingWithdrawalRequest->id;
            $settingWithdrawalRequestItem->slug = "minSumWithdrawalRequest";
            $settingWithdrawalRequestItem->name = "Минимальная сумма для вывода";
            $settingWithdrawalRequestItem->value = 500;
            $settingWithdrawalRequestItem->type = \backend\modules\system\models\SettingItem::FLOAT;
            $settingWithdrawalRequestItem->save();
        }


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \backend\modules\system\models\Setting::deleteAll(['slug' => 'withdrawalRequestSetting']);
    }

}
