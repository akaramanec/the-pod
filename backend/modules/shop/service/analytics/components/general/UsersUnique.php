<?php

namespace backend\modules\shop\service\analytics\components\general;

class UsersUnique extends AnalyticsGeneralItemModel
{
    /**
     * @param int $countCustomer
     */
    public function __construct(int $countCustomer)
    {
        $this->setDescription("Количество пользователей у которых есть хоть один успешно завершенный заказ");
        $this->setValue($countCustomer);
        parent::__construct();
    }

    public function init()
    {
        $this->setTitle('Уникальные покупатель');
        $this->setDimension('чел');
    }
}