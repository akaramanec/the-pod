<?php

namespace blog\models;

use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderPayBloggerFixed;
use src\helpers\Common;

class CacheBloggerFixed
{
    /**
     * @var CustomerBlog
     */
    public $blog;
    public $customerBlogger;
    public $lvl3Need = false;

    public function setCache($customerBlogger)
    {
        $this->blog = new CustomerBlog();
        $this->customerBlogger = $customerBlogger;
        $this->setLevelCustomer();
        if ($this->blog->customerLevel) {
            $this->payments();
        }
        $this->saveCache();
    }

    private function saveCache()
    {
        $cache = [
            'customerLevel' => $this->blog->customerLevel,
            'customerId' => $this->blog->customerId,
            'customerCount' => $this->blog->customerCount,
            'ordersCount' => $this->blog->ordersCount,
            'sumTotalOrders' => $this->blog->sumTotalOrders,
            'sumTotalPayed' => $this->blog->sumTotalPayed,
            'sumDebt' => $this->blog->sumDebt,
            'orders' => $this->blog->orders,
        ];
        CustomerBlog::updateAll(['cache' => $cache], ['customer_id' => $this->customerBlogger->id]);
    }

    private function queryOrders()
    {
        return Order::find()
            ->alias('order')
            ->where(['order.status' => Order::STATUS_CLOSE_SUCCESS])
            ->andWhere(['!=', 'order.customer_id', $this->customerBlogger->id])
            ->andWhere(['order.customer_id' => $this->blog->customerId])
            ->joinWith(['botCustomer AS botCustomer', 'orderCustomer AS orderCustomer'])
            ->with(['payBlogger'])
            ->orderBy('created_at desc');
    }

    private function payments()
    {
        $this->blog->sumTotalPayed = OrderPayBloggerFixed::find()->where(['customer_id' => $this->customerBlogger->id])->sum('sum');
        foreach ($this->queryOrders()->each() as $order) {
            $this->blog->ordersCount++;
            $sumItem = $this->sumItem($order);
            $this->blog->sumTotalOrders += $sumItem;
            $this->blog->orders[$order->id] = [
                'sumItem' => $sumItem,
                'payBloggerSum' => $order->payBlogger ? $order->payBlogger->sum : null,
                'cache_sum_total' => $order->cache_sum_total,
                'level' => $this->blog->customerLevel[$order->customer_id]['level']
            ];
        }
        $this->blog->sumDebt = $this->blog->sumTotalOrders - $this->blog->sumTotalPayed;
    }

    private function sumItem($order)
    {
        if (isset($this->blog->customerLevel[$order->customer_id])) {
            $sum = $order->cache_sum_total;
            if ($this->blog->customerLevel[$order->customer_id]['level'] == 1) {
                $percent = $this->getPercent($sum, $this->customerBlogger->blog->percent_level_1);
                return Common::percentFormatter($percent);
            }
            if ($this->blog->customerLevel[$order->customer_id]['level'] == 2) {
                $percent = $this->getPercent($sum, $this->customerBlogger->blog->percent_level_2);
                return Common::percentFormatter($percent);
            }
            if ($this->lvl3Need) {
                if ($this->blog->customerLevel[$order->customer_id]['level'] == 3) {
                    $percent = $this->getPercent($sum, $this->customerBlogger->blog->percent_level_3);
                    return Common::percentFormatter($percent);
                }
            }
        }
        return 0;
    }

    private function setLevelCustomer()
    {
        foreach ($this->queryChildren($this->customerBlogger->id)->each() as $l1) {
            $this->levelItem($l1, 1);
            foreach ($this->queryChildren($l1->id)->each() as $l2) {
                $this->levelItem($l2, 2);
                if ($this->lvl3Need) {
                    foreach ($this->queryChildren($l2->id)->each() as $l3) {
                        $this->levelItem($l3, 3);
                    }
                }
            }
        }
    }

    private function levelItem($customer, $level)
    {
        $this->blog->customerCount++;
        $this->blog->customerId[] = $customer->id;
        $this->blog->customerLevel[$customer->id] = [
            'id' => $customer->id,
            'parent_id' => $customer->parent_id,
            'level' => $level
        ];
    }


    private function queryChildren($parent_id)
    {
        return Customer::find()->where(['parent_id' => $parent_id])->andWhere(['status' => [
            Customer::STATUS_SUBSCRIBED,
            Customer::STATUS_ACTIVE,
            Customer::STATUS_UNSUBSCRIBED
        ]]);
    }

    /**
     * @param $sum
     * @param $percentSize
     * @return float|int
     */
    private function getPercent($sum, $percentSize)
    {
        return $sum - ((1 - $percentSize / 100) * $sum);
    }
}
