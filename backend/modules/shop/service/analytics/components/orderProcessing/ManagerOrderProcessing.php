<?php

namespace backend\modules\shop\service\analytics\components\orderProcessing;

use backend\modules\shop\service\analytics\components\orderProcessing\item\MangerProcessingItem;

class ManagerOrderProcessing implements ManagerOrderProcessingInterface
{
    /** @var MangerProcessingItem[] */
    public $managerItemsList;



    public function __construct(array $initParams)
    {
        $this->init($initParams);
    }

    public function init(array $initParams)
    {
        foreach ($initParams as $mangerProcessing) {
            $this->managerItemsList[] = new MangerProcessingItem($mangerProcessing);
        }
    }


    /**
     * @return string[]
     */
    public static function getTableHeaderTitles(): array
    {
        return [
            [
                '#' => ['rowspan' => '2'],
                'Менеджер' => ['rowspan' => '2', 'class' => 'fw-900'],
                'Кол-во заказов' => ['rowspan' => '2', 'class' => 'text-center fw-900'],
                'Скорость обработки заказов, мин' => ['colspan' => '3', 'class' => 'text-center fw-900'],
                '% отменненых заказов' => ['rowspan' => '2', 'class' => 'text-center fw-900']
            ],
            [
                '> обработке' => ['scope' => 'col', 'class' => 'text-center fw-900'],
                '> работе' => ['scope' => 'col', 'class' => 'text-center fw-900'],
                '> завершен' => ['scope' => 'col', 'class' => 'text-center fw-900'],
            ]
        ];
    }


    /**
     * @return string
     */
    public static function getTableDescription(): string
    {
        return "Статистика по статусам заказов";
    }
}