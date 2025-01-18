<?php

namespace backend\modules\shop\service\analytics\components\general;

class AverageCountOrder extends AnalyticsGeneralItemModel
{
    /**
     * @param int $countOrder
     * @param int $countCustomer
     */
    public function __construct(int $countOrder, int $countCustomer)
    {
        $averageSum = !empty($countCustomer) ? round(($countOrder / $countCustomer), 2) : 0;
        $this->setDescription("Сколько в среднем делает покупок один пользователь в чат-боте."
        );
        $this->setValue($averageSum);
        parent::__construct();
    }

    public function init()
    {
        $this->setTitle('Заказ');
        $this->setDimension('заказа / чел.');
    }
}