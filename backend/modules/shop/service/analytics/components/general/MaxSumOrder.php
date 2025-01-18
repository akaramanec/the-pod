<?php

namespace backend\modules\shop\service\analytics\components\general;

use backend\modules\shop\models\Order;
use Yii;
use yii\helpers\Html;

class MaxSumOrder extends AnalyticsGeneralItemModel
{
    /** @var Order $order */
    private $order;

    /**
     * @param float $maxSum
     */
    public function __construct(float $maxSum)
    {
        $this->setDescription("Показывает максимальную сумму заказа в указанном периоде.\n"
            . "Нажав на номер можна перейти на страницу заказа."
        );
        $this->setValue($maxSum);
        $this->setOrder(Order::findOne(['cache_sum_total' => $maxSum]));
        parent::__construct();
    }

    public function init()
    {
        $this->setTitle('Max');
        $this->setDimension('грн');
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    protected function htmlCartBodyTitle(): string
    {
        return <<<HTML
<p>
    <span class="text-value">{$this->getValue()}</span>
</p>
<p>
    <span class="text-dimension">{$this->getDimension()}</span>
    <span class="text-link">({$this->getOrderHtmlElement()})</span>
</p>
HTML;

    }

    /**
     * @return string
     */
    public function getOrderHtmlElement(): string
    {
        $order = $this->getOrder();
        return Html::a("Заказ №$order->id", ['/shop/order/update', 'id' => $order->id]);
        return Html::a("Заказ №$order->id - " . Yii::$app->formatter->format($order->created_at, 'date'), ['/shop/order/update', 'id' => $order->id]);
    }
}