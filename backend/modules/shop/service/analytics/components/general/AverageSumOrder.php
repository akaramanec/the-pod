<?php

namespace backend\modules\shop\service\analytics\components\general;

class AverageSumOrder extends AnalyticsGeneralItemModel
{
    /**
     * @param float $totalSum
     * @param int $countOrder
     */
    public function __construct(float $totalSum, int $countOrder)
    {

        $averageSum = ($countOrder !== 0) ? round(($totalSum / $countOrder), 2) : 0;
        $this->setDescription("Средняя сумма всех заказов со статусом “завершен”\n"
            . "Формула: сумма всех завершенных заказов / количество завершенных заказов"
        );
        $this->setValue($averageSum);
        parent::__construct();
    }

    public function init()
    {
        $this->setTitle('Средний чек');
        $this->setDimension('грн');
    }
}