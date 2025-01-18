<?php

namespace console\controllers;

use backend\modules\admin\models\AuthLogger;
use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\src\ApiNp;
use backend\modules\bot\src\DocumentNp;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\viber\VAdmin;
use backend\modules\shop\models\NoticeNp;
use backend\modules\shop\models\NoticeNpOrderLink;
use backend\modules\shop\models\Order;
use common\helpers\DieAndDumpHelper;
use src\email\NoticeNpNotification;
use src\services\np\CounterpartyModel;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class NpController extends Controller
{

    /**
     *  StateName => '[ 'Замовлення в обробці' 'Відправлено' 'Одержано' 'Прибув у відділення' 'Змінено адресу' 'Видалено' 'Відмова від отримання' ]
     */
    private $_noticeNp;

    public function actionCheckStatusEnNp()
    {
        try {
            $apiNp = new ApiNp();
            $documentNp = new DocumentNp($apiNp);
            $this->_noticeNp = NoticeNp::find()
                ->where(['status' => NoticeNp::STATUS_ACTIVE])
                ->orderBy('sort asc')
                ->indexBy('name')
                ->all();
            $orders = Order::find()->where(['status' => Order::STATUS_IN_WORK]);
            foreach ($orders->each() as $order) {
                try {
                    if ($order->customer->phone && isset($order->np->documentData['IntDocNumber'])) {
                        $phoneDocument = [[
                            'DocumentNumber' => $order->np->documentData['IntDocNumber'],
                            'Phone' => $order->customer->phone
                        ]];
                        $statusDocuments = $documentNp->getStatusDocuments($phoneDocument);
                        AuthLogger::saveNp(['att' => '$statusDocuments', 'order' => $order, 'statusDocuments' => $statusDocuments]);
                        if ($statusDocuments->success == true) {
                            $statusCode = $this->getStatusCode($statusDocuments, $order->np->documentData['IntDocNumber']);
                            if (!$statusCode) {
                                continue;
                            }
                            $statusCodeName = $this->getStatusCodeText($statusCode);
                            if (!$statusCodeName) {
                                continue;
                            }
                            $nnol = NoticeNpOrderLink::findOne(['notice_np_name' => $statusCodeName, 'order_id' => $order->id]);
                            if ($nnol) {
                                continue;
                            }
                            if ($statusCodeName == 'Нова пошта очікує надходження від відправника') {
                                AuthLogger::saveNp(['att' => 'Нова пошта очікує надходження від відправника', 'order' => $order, 'statusDocuments' => $statusDocuments]);
                                $this->sendNoticeNp($statusCodeName, $order);
                                continue;
                            }
                            if ($statusCodeName == 'Відправлено') {
                                AuthLogger::saveNp(['att' => 'Відправлено', 'order' => $order, 'statusDocuments' => $statusDocuments]);
                                $this->sendNoticeNp($statusCodeName, $order);
                                continue;
                            }
                            if ($statusCodeName == 'Прибув у відділення') {
                                AuthLogger::saveNp(['att' => 'Прибув у відділення', 'order' => $order, 'statusDocuments' => $statusDocuments]);
                                $this->sendNoticeNp($statusCodeName, $order);
                                continue;
                            }
                            if ($statusCodeName == 'Відправлення отримано') {
                                AuthLogger::saveNp(['att' => 'Відправлення отримано', 'order' => $order, 'statusDocuments' => $statusDocuments]);
                                $order->status = Order::STATUS_CLOSE_SUCCESS;
                                $order->detachBehaviors();
                                $order->save(false);
                                $this->sendNoticeNp($statusCodeName, $order);
                                continue;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            BotLogger::save_input(['status' => 'ok'], __METHOD__);
        } catch (\Exception $e) {
            BotLogger::save_input([
                'status' => 'error',
                'message' => $e->getMessage()
            ], __METHOD__);
            Yii::$app->errorHandler->logException($e);
        }
    }

    private function sendNoticeNp($statusCodeName, $order)
    {
        if (isset($this->_noticeNp[$statusCodeName]) && isset($order->customer->bot->platform)) {
            $noticeNp = $this->_noticeNp[$statusCodeName];
            $nnol = new NoticeNpOrderLink();
            $nnol->notice_np_name = $statusCodeName;
            $nnol->order_id = $order->id;
            $nnol->save();
            if ($order->customer->bot->platform == Bot::TELEGRAM) {
                Yii::$app->tm->customer = $order->customer;
                Yii::$app->tm->platformId = $order->customer->platform_id;
                $admin = new TAdmin();
                $admin->noticeNp($noticeNp, $order->customer);
            }
            if ($order->customer->bot->platform == Bot::VIBER) {
                Yii::$app->vb->customer = $order->customer;
                Yii::$app->vb->platformId = $order->customer->platform_id;
                $admin = new VAdmin();
                $admin->noticeNp($noticeNp, $order->customer);
            }
            try {
                new NoticeNpNotification($noticeNp, $order);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
            }
        }
    }

    private function getStatusCodeText($statusCode)
    {
        foreach ($this->_noticeNp as $item) {
            if (ArrayHelper::isIn((int)$statusCode, $item->status_code)) {
                return $item->name;
            }
        }
    }

    private function getStatusCode($statusDocuments, $intDocNumber)
    {
        if (isset($statusDocuments->data) && !empty($statusDocuments->data)) {
            foreach ($statusDocuments->data as $item) {
                if ($item->Number == $intDocNumber) {
                    return (int)$item->StatusCode;
                }
            }
        }
    }

    public function actionTest()
    {
        AuthLogger::saveNp(['att' => 'Відправлення отримано']);
    }

    public function actionSenderAddress()
    {
        DieAndDumpHelper::dd(CounterpartyModel::getCounterpartyAddresses());
    }

    public function actionCheckOrderNpStatus($order_id)
    {
        $apiNp = new ApiNp();
        $documentNp = new DocumentNp($apiNp);
        $this->_noticeNp = NoticeNp::find()
            ->where(['status' => NoticeNp::STATUS_ACTIVE])
            ->orderBy('sort asc')
            ->indexBy('name')
            ->all();
        $order = Order::findOne(['id' => $order_id]);
        $phoneDocument = [[
            'DocumentNumber' => $order->np->documentData['IntDocNumber'],
            'Phone' => $order->customer->phone
        ]];
        $statusDocuments = $documentNp->getStatusDocuments($phoneDocument);
        $statusCode = $this->getStatusCode($statusDocuments, $order->np->documentData['IntDocNumber']);
        $statusCodeName = $this->getStatusCodeText($statusCode);

        DieAndDumpHelper::dd($statusCode, $statusCodeName, $statusDocuments);
    }
}
