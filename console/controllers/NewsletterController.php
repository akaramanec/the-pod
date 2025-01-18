<?php

namespace console\controllers;

use backend\modules\bot\models\Logger;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\Newsletter;
use backend\modules\customer\models\NewsletterMessages;
use backend\modules\customer\models\search\CustomerSearchNewsletter;
use Exception;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\console\Controller;

class NewsletterController extends Controller
{
    /**
     * @var Newsletter
     */
    private $newsletter;
    /**
     * @var array|bool[]
     */
    private $sentResponse;

    public function actionIndex()
    {
        echo 'start' . PHP_EOL;
        $this->checkNewsletter();
        $this->send();
        echo 'finish' . PHP_EOL;
    }

    private function checkNewsletter(): void
    {
        echo 'checkNewsletter' . PHP_EOL;
        $this->newsletter = Newsletter::find()
            ->where(['status' => Newsletter::STATUS_SEND])
            ->andWhere(['<=', 'date_departure', Yii::$app->common->datetimeNow])
            ->orderBy('created_at asc')
            ->limit(1)->one();
        if ($this->newsletter === null) {
            exit();
        }
        $this->newsletter->status = Newsletter::STATUS_IN_WORK;
        $this->newsletter->save(false);
        $this->newsletter->setSetting();
    }

    public function send()
    {
        echo 'send' . PHP_EOL;
        $customerSearchNewsletter = new CustomerSearchNewsletter($this->newsletter);
        $search = $customerSearchNewsletter->search();
        if ($search->exists()) {
            foreach ($search->batch(30) as $customers) {
                $this->sendNewsletter($customers);
            }
        }
        $newsletterCustomerCount = $this->newsletterCustomerCount();
        $search = $customerSearchNewsletter->search(false);
        $countCustomer = $search->count();
        if ($newsletterCustomerCount >= $countCustomer) {
            $this->newsletter->status = Newsletter::STATUS_SENT;
            $this->newsletter->save();
            Logger::commit(['status' => 'ok'], __METHOD__);
        }
    }

    private function sendNewsletter(array $customers): void
    {
        foreach ($customers as $customer) {
            try {
                if ($this->sendMessage($customer)) {
                    continue;
                }

                if (isset($this->sentResponse['error_code']) &&
                    $this->sentResponse['error_code'] == 429 &&
                    isset($this->sentResponse['parameters']['retry_after']) &&
                    $this->sentResponse['parameters']['retry_after']) {
                    sleep($this->sentResponse['parameters']['retry_after']);
                    if ($this->sendMessage($customer)) {
                        continue;
                    }
                }

                $this->saveNewsletterCustomer($customer, $this->sentResponse, Newsletter::STATUS_ERROR);
            } catch (Exception $e) {
                $this->saveNewsletterCustomer($customer, $e->getMessage(), Newsletter::STATUS_ERROR);
                continue;
            }
        }
        sleep(1);
    }

    private function sendMessage(Customer $customer)
    {
        $TAdmin = new TAdmin();
        Yii::$app->tm->setCustomer($customer);
        Yii::$app->tm->platformId = $customer->platform_id;
        $this->sentResponse = $TAdmin->newsletter($this->newsletter);
        if (isset($this->sentResponse['ok']) && $this->sentResponse['ok'] == true) {
            $this->saveNewsletterCustomer($customer, null, Newsletter::STATUS_SENT);
            return true;
        }
    }

    public function saveNewsletterCustomer(Customer $customer, $data, $status)
    {
        if (NewsletterMessages::find()
            ->where(['customer_id' => $customer->id])
            ->andWhere(['newsletter_id' => $this->newsletter->id])
            ->exists()) {
            return;
        }
        $model = new NewsletterMessages();
        $model->customer_id = $customer->id;
        $model->newsletter_id = $this->newsletter->id;
        $model->data = $data;
        $model->status = $status;
        $model->save();
    }

    public function newsletterCustomerCount()
    {
        return NewsletterMessages::find()
            ->where(['newsletter_id' => $this->newsletter->id])
            ->count();
    }
}
