<?php

namespace backend\modules\shop\service\analytics\components\status;

abstract class AnalyticsStatusItemModel implements AnalyticsStatusItemInterface
{
    /** @var string */
    private $tableDescription;

    /**
     * @var string $title
     */
    private $title;

    /** @var int $status */
    private $status;

    /** @var int $status */
    private $count;

    /** @var float $dimension */
    private $percent;

    /**
     * @param int $count
     * @param int $allCount
     */
    public function __construct(int $count, int $allCount)
    {
        $percent = !empty($allCount) ? round((($count / $allCount) * 100), 2) : 0;
        $this->setPercent($percent);
        $this->setCount($count);
        $this->init();
    }

    /**
     * @return string[]
     */
    public static function getTableHeaderTitles(): array
    {
        return [
            '#' => ['scope' => 'col'],
            'Статус' => ['scope' => 'col', 'class' => 'fw-900'],
            'Кол-во' => ['scope' => 'col', 'class' => 'text-center fw-900'],
            '% от общего' => ['scope' => 'col', 'class' => 'text-center fw-900']
        ];
    }

    /**
     * @return string
     */
    public static function getTableDescription(): string
    {
        return "Cкорость изменения всех статусов заказа.\n"
            . "Учет времени изменения статусов считается только с 9:00 по 21:00";
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count)
    {
        $this->count = $count;
    }

    /**
     * @return float
     */
    public function getPercent(): float
    {
        return $this->percent;
    }

    /**
     * @param float $percent
     */
    public function setPercent(float $percent)
    {
        $this->percent = $percent;
    }


}