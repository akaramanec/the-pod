<?php

namespace backend\modules\customer\models\search;

use backend\modules\customer\models\Newsletter;
use backend\modules\customer\models\NewsletterMessages;
use common\helpers\DieAndDumpHelper;
use Yii;
use backend\modules\bot\models\Bot;
use backend\modules\customer\models\CustomerTagLink;
use yii\data\ActiveDataProvider;
use backend\modules\customer\models\Customer;
use yii\helpers\ArrayHelper;

class CustomerSearchNewsletter
{
    const SITE_CUSTOMER_ID = 1;
    public $newsletter;
    public $dataProvider;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function search($excludeSent = true)
    {
        $query = Customer::find()
            ->alias('customer')
            ->joinWith(['bot AS bot']);

        if ($this->newsletter->subscribedCustomer == $this->newsletter->activeCustomer) {
            $query->andWhere(['in', 'customer.status', [Customer::STATUS_SUBSCRIBED, Customer::STATUS_ACTIVE]]);
        }  else {
            if ($this->newsletter->subscribedCustomer) {
                $query->andWhere(['customer.status' => Customer::STATUS_SUBSCRIBED]);
            }

            if ($this->newsletter->activeCustomer) {
                $query->andWhere(['customer.status' => Customer::STATUS_ACTIVE]);
            }
        }

        $platform = [];
        if ($this->newsletter->sendTelegram) {
            $platform[] = Bot::TELEGRAM;
        }
        if ($this->newsletter->sendViber) {
            $platform[] = Bot::VIBER;
        }
        if ($platform) {
            $query->andWhere(['bot.platform' => $platform]);
        }

        $blogger = [];
        if ($this->newsletter->customerBlogger) {
            $blogger[] = Customer::BLOGGER_TRUE;
        }
        if ($this->newsletter->notCustomerBlogger) {
            $blogger[] = Customer::BLOGGER_FALSE;
        }
        if ($blogger) {
            $query->andWhere(['customer.blogger' => $blogger]);
        }

        if ($this->newsletter->tagsId) {
            $query->andWhere(['customer.id' => $this->customerIdByTagId()]);
        }

        if ($excludeSent) {
            if ($ids = $this->sentCustomerIds()) {
                $query->andWhere(['not', ['in', 'customer.id', $ids]]);
            }
        }
        return $query->andWhere(['not', ['customer.id' => self::SITE_CUSTOMER_ID]]);
    }

    public function setDataProvider()
    {
        $query = $this->search();
        $this->dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $this->dataProvider->setPagination(['pageSize' => false]);
    }

    private function customerIdByTagId()
    {
        return ArrayHelper::getColumn(CustomerTagLink::find()
            ->alias('ctl')
            ->where(['ctl.tag_id' => $this->newsletter->tagsId])
            ->joinWith(['customer AS customer'])
            ->asArray()
            ->andWhere(['customer.status' => Customer::STATUS_ACTIVE])
            ->groupBy('customer_id')
            ->all(), 'customer_id');
    }

    private function sentCustomerIds()
    {
        return ArrayHelper::getColumn(NewsletterMessages::find()
            ->select(['customer_id'])
            ->where(['newsletter_id' => $this->newsletter->id])
            ->groupBy('customer_id')
            ->asArray()
            ->all(), 'customer_id');
    }
}
