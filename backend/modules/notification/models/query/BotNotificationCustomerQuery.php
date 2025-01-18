<?php

namespace backend\modules\notification\models\query;

/**
 * This is the ActiveQuery class for [[\backend\modules\notification\models\db\BotNotificationCustomer]].
 *
 * @see \backend\modules\notification\models\db\BotNotificationCustomer
 */
class BotNotificationCustomerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \backend\modules\notification\models\db\BotNotificationCustomer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \backend\modules\notification\models\db\BotNotificationCustomer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
