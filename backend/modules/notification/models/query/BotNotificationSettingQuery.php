<?php

namespace backend\modules\notification\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\notification\models\db\BotNotificationSetting]].
 *
 * @see \backend\modules\notification\models\db\BotNotificationSetting
 */
class BotNotificationSettingQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \backend\modules\notification\models\db\BotNotificationSetting[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \backend\modules\notification\models\db\BotNotificationSetting|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
