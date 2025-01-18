<?php

namespace backend\modules\shop\service\analytics\components\orderProcessing\item;

class MangerProcessingItem
{
    public $mangerId;
    public $managerName;
    public $timeSuccessStatus;
    public $timeInWorkStatus;
    public $timeInProcessingStatus;
    public $countAllOrders;
    public $countCanceledOrders;
    public $countInSuccessStatus;
    public $countInInWorkStatus;
    public $countInInProcessingStatus;
    public $isVisible = false;

    public function __construct(array $initParams)
    {
        $this->init($initParams);
    }

    public function init(array $initParams)
    {
        $attributesObject = array_keys(get_object_vars($this));
        foreach ($attributesObject as $attribute) {
            if (!empty($initParams[$attribute])) {
                $this->$attribute = $initParams[$attribute];
            }
        }

        if (!empty($this->mangerId) &&
            (!empty($this->timeSuccessStatus) ||
                !empty($this->timeInWorkStatus) ||
                !empty($this->timeInProcessingStatus))
        ) {
            $this->isVisible = true;
        }

        if (!empty($this->timeSuccessStatus) && !empty($this->countInSuccessStatus)) {
            $minutes = round($this->timeSuccessStatus / $this->countInSuccessStatus, 0);
            $this->timeSuccessStatus = date('H:i', mktime(0, $minutes));
        }
        if (!empty($this->timeInWorkStatus) && !empty($this->countInInWorkStatus)) {
            $minutes = round($this->timeInWorkStatus / $this->countInInWorkStatus, 0);
            $this->timeInWorkStatus = date('H:i', mktime(0, $minutes));
        }
        if (!empty($this->timeInProcessingStatus) && !empty($this->countInInProcessingStatus)) {
            $minutes = round($this->timeInProcessingStatus / $this->countInInProcessingStatus, 0);
            $this->timeInProcessingStatus = date('H:i', mktime(0, $minutes));
        }
    }

    /**
     * @return float
     */
    public function getPercentCanceledOrder(): float
    {
        if (empty($this->countAllOrders) || empty($this->countCanceledOrders)) {
            return floatval(0);
        }
        return round((($this->countCanceledOrders / $this->countAllOrders) * 100), 2);
    }
}