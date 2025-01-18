<?php

namespace backend\modules\notification\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\notification\models\db\BotNotification]].
 *
 * @see \backend\modules\notification\models\db\BotNotification
 */
class BotNotificationQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \backend\modules\notification\models\db\BotNotification[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \backend\modules\notification\models\db\BotNotification|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
