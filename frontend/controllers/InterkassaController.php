<?php

namespace frontend\controllers;

use backend\modules\bot\models\Bot;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use backend\modules\system\models\Interkassa;
use backend\modules\system\models\Setting;
use frontend\models\cart\Cart;
use frontend\models\cart\CartData;
use src\services\data\PlatformData;
use src\services\InterkassaService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class InterkassaController extends Controller
{
    public $layout = false;
    public $enableCsrfValidation = false;

    public function actionIndex($order_id)
    {
        Interkassa::setModel($order_id);
        $order = $this->findOrder($order_id);
        $cart = new Cart();
        $cart->build(new CartData($order));
        $platform = isset($order->customer->bot->platform) ? $order->customer->bot->platform : Customer::CUSTOMER_SITE;
        $platformData = new PlatformData($platform, $order);
        return $this->render('index', [
            'order' => $order,
            'setting' => Setting::listValue('interkassa'),
            'cart' => $cart,
            'platformData' => $platformData,
        ]);
    }

    public function findOrder($order_id)
    {
        $order = Order::find()
            ->where(['id' => $order_id])
            ->andWhere(['status' => [Order::STATUS_NEW_SITE, Order::STATUS_NEW]])
            ->limit(1)->one();
        if ($order) {
            return $order;
        }
        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }

    public function actionPayCallback()
    {
        $data = Yii::$app->request->post();
        $setting = Setting::listValue('interkassa');
        $needle = $data['ik_sign'];

        unset($data['ik_sign']);
        ksort($data, SORT_STRING);
        $test_key = $setting['secret_key'];
        array_push($data, $test_key);
        $signString = implode(':', $data);
        $sign = base64_encode(md5($signString, true));
        if ($needle == $sign) {
            $service = Yii::createObject([
                'class' => InterkassaService::class
            ]);
            $id = explode('-', $data['ik_pm_no']);
            $service->model = Interkassa::findOne($id[0]);
            if ($service->statusSuccess($data)) {
                return true;
            }
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
