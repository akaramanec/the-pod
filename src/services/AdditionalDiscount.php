<?php

namespace src\services;

class AdditionalDiscount
{
    const DATE_FORMAT = "Y-m-d H:i:s";

    /** @var bool */
    protected $status = false;

    /** @var string $dateStart */
    protected $dateStart;

    /** @var string $dateEnd */
    protected $dateEnd;

    /** @var float $percent */
    protected $percent;

    /** @var string $brandDiscount */
    protected $brandDiscount;

    /** @var string $brandPuff */
    protected $brandPuff;

    /** @var string $message */
    protected $message;

    /** @var string $orderDate */
    protected $orderDate;

    /** @var float $orderDate */
    protected $sum;

    /** @var float $mainDiscount */
    protected $mainDiscount;

    /** @var string $productName */
    protected $productName;

    /**
     * @param string $orderDate
     * @param $sum
     * @param float $mainDiscount
     */
    public function __construct(string $productName, string $orderDate, $sum, $mainDiscount = 0)
    {
        $this->init();
        $this->orderDate = date(self::DATE_FORMAT, strtotime($orderDate));
        $this->sum = floatval($sum);
        $this->mainDiscount = $this->calculateDiscountCoefficient($mainDiscount);
        $this->productName = $productName;
    }

    private function init()
    {
        if (isset(\Yii::$app->params['additionalDiscount'])) {
            $settings = \Yii::$app->params['additionalDiscount'];
            $this->status = $settings['status'];
            $this->message = $settings['message'];
            $this->dateStart = date(self::DATE_FORMAT, strtotime($settings['dateStart']));
            $this->dateEnd = date(self::DATE_FORMAT, strtotime($settings['dateEnd']));
            $this->percent = $this->calculateDiscountCoefficient((floatval($settings['percent'])));
            $this->brandDiscount = $settings['brandDiscount'];
            $this->brandPuff = $settings['brandPuff'] ?? '';
        }
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        $isDiscountProduct = false;
        if (mb_stripos($this->productName, $this->brandDiscount) !== false
            && (!empty($this->brandPuff) && mb_stripos($this->productName, $this->brandPuff) !== false)
        ) {
            $isDiscountProduct = true;
        }
        return ($this->status && $isDiscountProduct && ($this->dateStart <= $this->orderDate) && ($this->dateEnd >= $this->orderDate));
    }

    /**
     * @return float
     */
    public function sumWithDiscount(): float
    {
        $discountCoefficient = $this->mainDiscount + ($this->isActive() ? $this->percent : 0);
        return round(($this->sum * (1 - $discountCoefficient)), 2);
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->isActive() ? $this->percent * 100 : 0;
    }

    /**
     * @return float
     */
    public function sumDiscount(): float
    {
        $discountCoefficient = $this->mainDiscount + ($this->isActive() ? $this->percent : 0);
        return round(($this->sum * $discountCoefficient), 2);
    }

    /**
     * @return string
     */
    public static function getMessage(string $orderDate): string
    {
        $message = '';

        if (isset(\Yii::$app->params['additionalDiscount'])) {
            $settings = \Yii::$app->params['additionalDiscount'];
            $status = $settings['status'];
            $dateStart = date(self::DATE_FORMAT, strtotime($settings['dateStart']));
            $dateEnd = date(self::DATE_FORMAT, strtotime($settings['dateEnd']));
            $dateCurrent = date(AdditionalDiscount::DATE_FORMAT, strtotime($orderDate));

            if ($status && ($dateStart <= $dateCurrent) && ($dateEnd >= $dateCurrent)) {
                $message .= "\n" . $settings['message'];
            }

        }

        return $message;
    }

    /**
     * @param float $percent
     * @return float
     */
    private function calculateDiscountCoefficient(float $percent): float
    {
        return round(($percent / 100), 4);
    }

    /* Тимчасово до 07.11.21 нада знижка на фронті для 1500 puff */
    public static function updateProductItemPrice(float &$price, string $productName, $isUpdate = false)
    {
        if (!$isUpdate) return;

        if (isset(\Yii::$app->params['additionalDiscount'])) {
            $settings = \Yii::$app->params['additionalDiscount'];
            $brandDiscount = $settings['brandDiscount'];
            $brandPuff = $settings['brandPuff'] ?? '';
            $status = $settings['status'];
            $dateStart = date(self::DATE_FORMAT, strtotime($settings['dateStartFront']));
            $dateEnd = date(self::DATE_FORMAT, strtotime($settings['dateEndFrond']));
            $dateCurrent = date(AdditionalDiscount::DATE_FORMAT);
            if ($status && mb_stripos($productName, $brandDiscount) !== false
                && (!empty($productName) && mb_stripos($productName, $brandPuff) !== false)
                && ($dateStart <= $dateCurrent) && ($dateEnd >= $dateCurrent)
            ) {
                $price = $settings['sumFront'];
            }
        }

        $price = number_format($price, 2);
    }
}