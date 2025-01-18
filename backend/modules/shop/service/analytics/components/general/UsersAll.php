<?php

namespace backend\modules\shop\service\analytics\components\general;

class UsersAll extends AnalyticsGeneralItemModel
{
    /**
     * @param int $countCustomer
     */
    public function __construct(int $countCustomer)
    {
        $this->setDescription("Общее количество пользователей");
        $this->setValue($countCustomer);
        parent::__construct();
    }

    public function init()
    {
        $this->setTitle('Всего пользователей');
        $this->setDimension('чел');
    }
}