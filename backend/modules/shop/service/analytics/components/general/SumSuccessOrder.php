<?php

namespace backend\modules\shop\service\analytics\components\general;

class SumSuccessOrder extends AnalyticsGeneralItemModel
{
    /**
     * @param float $totalSum
     */
    public function __construct(float $totalSum)
    {
        $this->setValue($totalSum);
        parent::__construct();
    }
    public function init()
    {
        $this->setDescription("Сумма оплаченных счетов заказов со статусом “завершен”");
        $this->setTitle('Сума успешных заказов');
        $this->setDimension('грн');
    }
}