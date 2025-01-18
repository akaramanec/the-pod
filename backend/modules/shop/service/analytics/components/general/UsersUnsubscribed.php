<?php

namespace backend\modules\shop\service\analytics\components\general;

class UsersUnsubscribed extends AnalyticsGeneralItemModel
{
    /**
     * @param int $countCustomer
     */
    public function __construct(int $countCustomer)
    {
        $this->setDescription("Пользователи которые отписались от чат бота");
        $this->setValue($countCustomer);
        parent::__construct();
    }

    public function init()
    {
        $this->setTitle('Всего отписалось');
        $this->setDimension('чел');
    }
}