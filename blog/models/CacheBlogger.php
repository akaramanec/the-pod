<?php

namespace blog\models;

use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderPayBlogger;
use src\validators\Is;
use Yii;

class CacheBlogger
{

    public $blog;
    public $customerBlogger;

    public function setCache($customerBlogger)
    {
        $this->blog = new CustomerBlog();
        $this->customerBlogger = $customerBlogger;
        $this->setLevelCustomer();
        if ($this->blog->customerLevel) {
            $this->payments();
            $this->checkSumDebt();
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
            'checkSumDebt' => $this->blog->checkSumDebt,
            'payAll' => $this->blog->payAll,
            'orders' => $this->blog->orders,
            'ordersIdNoPay' => $this->blog->ordersIdNoPay,
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
        foreach ($this->queryOrders()->each() as $order) {
            $this->blog->ordersCount++;
            $sumItem = $this->sumItem($order);
            $this->blog->sumTotalOrders += $sumItem;
            if ($order->payBlogger) {
                $this->blog->sumTotalPayed += $order->payBlogger->sum;
                if (isset($this->blog->payAll[$order->payBlogger->created_at])) {
                    $this->blog->payAll[$order->payBlogger->created_at] += $order->payBlogger->sum;
                } else {
                    $this->blog->payAll[$order->payBlogger->created_at] = $order->payBlogger->sum;
                }
            } else {
                $this->blog->ordersIdNoPay[] = $order->id;
            }

            $this->blog->orders[$order->id] = [
                'sumItem' => $sumItem,
                'payBloggerSum' => $order->payBlogger ? $order->payBlogger->sum : null,
                'cache_sum_total' => $order->cache_sum_total,
                'level' => $this->blog->customerLevel[$order->customer_id]['level']
            ];
        }
        $this->blog->sumDebt = $this->blog->sumTotalOrders - $this->blog->sumTotalPayed;
    }

    private function orders($customer, $level)
    {
        $this->blog->customerLevel[$customer->id] = [
            'id' => $customer->id,
            'parent_id' => $customer->parent_id,
            'level' => $level
        ];
    }

    private function checkSumDebt()
    {
        if ($this->blog->sumDebt > 0) {
            $this->blog->checkSumDebt = true;
        }
    }

    private function sumItem($order)
    {
        if (isset($this->blog->customerLevel[$order->customer_id])) {
            if ($this->blog->customerLevel[$order->customer_id]['level'] == 1) {
                $percent = $order->cache_sum_total - ((1 - $this->customerBlogger->blog->percent_level_1 / 100) * $order->cache_sum_total);
                return $this->formatter($percent);
            }
            if ($this->blog->customerLevel[$order->customer_id]['level'] == 2) {
                $percent = $order->cache_sum_total - ((1 - $this->customerBlogger->blog->percent_level_2 / 100) * $order->cache_sum_total);
                return $this->formatter($percent);
            }
        }
        return 0;
    }

    private function formatter($n)
    {
        $num = 0;
        $e = explode('.', $n);
        if (isset($e[0])) {
            $num = $e[0];
        }
        if (isset($e[1]) && $e[1]) {
            $i = (int)(substr($e[1], 0, 2));
            if ($i) {
                $num = $num . '.' . $i;
            }
        }
        $num = (float)$num;
        if ($num) {
            return $num;
        }
        return 0;
    }

    public function setPay($order_id, $blogger_id)
    {
        $this->customerBlogger = Customer::findOne($blogger_id);
        $this->blog = $this->customerBlogger->blog;
        $created_at = Yii::$app->common->datetimeNow;
        foreach (Order::find()->where(['id' => $order_id])->all() as $order) {
            $orderPayBlogger = new OrderPayBlogger();
            $orderPayBlogger->sum = $this->sumItem($order);
            $orderPayBlogger->order_id = $order->id;
            $orderPayBlogger->created_at = $created_at;
            if (!$orderPayBlogger->save()) {
                Is::errors($orderPayBlogger->errors);
            }
        }
        $this->setCache($this->customerBlogger);
    }

    public function setPayWholesale($blogger_id)
    {
        $q = Customer::find()->where(['id' => $blogger_id])->with(['blog']);
        foreach ($q->each() as $customer) {
            if ($customer->blog->ordersIdNoPay) {
                $this->setPay($customer->blog->ordersIdNoPay, $customer->id);
            }
        }
    }

    private function setLevelCustomer()
    {
        foreach ($this->queryChildren($this->customerBlogger->id)->each() as $l1) {
            $this->levelItem($l1, 1);
            foreach ($this->queryChildren($l1->id)->each() as $l2) {
                $this->levelItem($l2, 2);
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
        return Customer::find()->where(['parent_id' => $parent_id]);
    }

}
