<?php

namespace backend\modules\shop\service\analytics\components\general;

class SuccessOrder extends AnalyticsGeneralItemModel
{
    /** @var float */
    protected $percentValue;

    /**
     * @param int $countOrder
     * @param int $allCount
     */
    public function __construct(int $countOrder, int $allCount)
    {
        $this->percentValue = ($countOrder) != 0 ? round((($countOrder / $allCount) * 100), 2) : 0;
        $this->setValue($countOrder);
        parent::__construct();
    }

    public function init()
    {
        $this->setDescription("Средняя сумма всех заказов со статусом “завершен”\n"
            . "Формула: сумма всех завершенных заказов / количество завершенных заказов"
        );
        $this->setTitle('Успешных заказов');
        $this->setDimension('шт.');
    }

    /**
     * @return float
     */
    public function getPercentValue()
    {
        return $this->percentValue . " %";
    }
    
    protected function htmlCartBodyTitle(): string
    {
        return <<<HTML
        <p>
            <span class="text-value">{$this->getValue()}</span>
        </p>
        <p>
            <span class="text-dimension">{$this->getDimension()}</span>
            <span class="general-percent-value">{$this->getPercentValue()}</span>
        </p>
HTML;

    }
}