<?php

namespace frontend\controllers;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\viber\VAdmin;
use backend\modules\shop\models\Order;
use backend\modules\system\models\Fondy;
use Exception;
use frontend\models\cart\Cart;
use frontend\models\cart\CartData;
use src\services\data\PlatformData;
use src\services\FondyService;
use Yii;
use yii\rest\Controller;

/**
 * @property FondyService $_fondyObject
 */
class FondyController extends Controller
{
    private $_fondyObject;

    public function init()
    {
        parent::init();
        $this->_fondyObject = Yii::createObject([
            'class' => FondyService::class
        ]);
    }

    public function actionIndex()
    {
        $result = new \Cloudipsp\Result\Result();
        $data = $result->getData();
        BotLogger::save_input([$data]);
        if ($data) {
            $fondy = Fondy::find()->where(['time_id' => $data['order_id']])->limit(1)->one();
            if ($data['response_status'] == 'success') {
                $this->_fondyObject->statusSuccess($fondy, $data);
            }
        }
    }

    public function actionSendPay($order_id)
    {
        try {
            $this->_fondyObject->fondyModel = Fondy::setModel($order_id);
            if ($this->_fondyObject->fondyModel) {
                $this->_fondyObject->platformData = new PlatformData($this->_fondyObject->fondyModel->order->customer->bot->platform);
                $cart = new Cart();
                $cart->build(new CartData($this->_fondyObject->fondyModel->order));
                $this->_fondyObject->cart = $cart;
                $this->_fondyObject->checkout();
                return $this->redirect($this->_fondyObject->urlCheckout);
            } else {
                throw new \Exception('No model fondy');
            }
        } catch (Exception $e) {
            BotLogger::save_input([$e->getMessage()]);
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->params['homeUrl'] . '/error/message');
        }
    }

    public function actionViber()
    {
        $platformData = new PlatformData(Bot::VIBER);
        return $this->redirect($platformData->redirectInBotOnlinePay);
    }

    public function actionTelegram()
    {
        $platformData = new PlatformData(Bot::TELEGRAM);
        return $this->redirect($platformData->redirectInBotOnlinePay);
    }
}
