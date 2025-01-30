<?php

namespace src\helpers;

use backend\modules\admin\models\AuthLogger;
use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\models\BotSession;
use backend\modules\bot\models\Logger;
use backend\modules\bot\src\ApiNp;
use backend\modules\bot\src\SenderNp;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\telegram\TBaseCommon;
use backend\modules\bot\viber\VAdmin;
use backend\modules\bot\viber\VBaseCommon;
use backend\modules\customer\models\ClickStatistic;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\CustomerTagLink;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderPayBlogger;
use blog\models\CacheBlogger;
use Yii;

class Demo
{

    public function __construct($id)
    {
//        $this->blogCache();
        $this->tm($id);
//        $this->vb();
//        $this->registration();
//        $this->moveReferral();
//        $this->np();


    }

    public function tm($id = null)
    {
        $log = Logger::findOne($id);
        Yii::$app->tm->input = json_decode(json_encode($log->data));
        Yii::$app->tm->run();
    }

    public function vb()

    {
        $log = Logger::findOne(92);
        Yii::$app->vb->input = json_decode(json_encode($log->data));
        Yii::$app->vb->run();
    }

    public function np()
    {
        $apiNp = new ApiNp();
        $senderNp = new SenderNp($apiNp);
    }

    public function blogCache()
    {
        $customer = Customer::findOne(170);
//                $cacheBlogger = new CacheBlogger();
//                $cacheBlogger->setCache($customer);
        \yii\helpers\VarDumper::dump($customer->blog->cache, 1000, 5);
        die;
    }

    public function delKeyboardGroup()
    {
        $b = new \backend\modules\bot\telegram\TBaseCommon();
        $b->keyboardDeleteByPlatformId(Yii::$app->name, Yii::$app->params['groupTmChatId']);
    }

    public function registration()
    {
//        foreach (Customer::find()->each() as $customer) {
//            if ($this->checkInactiveCustomer($customer)) {
//                $customer->status = Customer::STATUS_NEW;
//                $customer->save(false);
//            }
//        }
    }

    public function checkInactiveCustomer($customer)
    {
        return (!$customer->phone ||
                !$customer->first_name ||
                !$customer->last_name ||
                !$customer->email) &&
            $customer->status == Customer::STATUS_INACTIVE;
    }

    public function keyboardDelete()
    {
        $b = new TBaseCommon();
        $b->keyboardDeleteByPlatformId('#', -1001360926148);
    }

    public function moveReferral()
    {
        $customers = Customer::find()
            ->where(['between', 'created_at', '2021-05-01 14:00:00', '2021-05-02 14:00:00'])
            ->andWhere(['parent_id' => 57])
            ->all();
        \yii\helpers\VarDumper::dump(count($customers), 1000, 5);
        die;
        foreach ($customers as $customer) {
            $customer->parent_id = 444;
//            $customer->save(false);
        }
    }

    public function news()
    {
        $text = 'Привет👋 У ThePod для тебя есть хорошие новости!
Многие  говорят о сложностях бота🤷🏻‍♂️
 Мы слышим своих клиентов, мы на данный момент упростили регистрацию на сколько это было возможно, теперь чтоб стать нашим клиентов тебе нужно всего лишь «поделиться номером», а мы в свою очередь поделимся с тобой скидкой-10%☺️Все честно🙌🏻
  Тебе и этого мало чтоб стать нашим клиентом? Хорошо! Мы снижаем цены на всю линейку Elf Bar 1500 и Vaporlax 😇
Думаю это ты точно оценишь!';

        $customers = Customer::find()->where(['status' => Customer::STATUS_NEW]);
//        $customers = Customer::find()->with(['bot'])->where(['id' => [12980, 62]]);
        $x = 0;
        foreach ($customers->each() as $customer) {
            try {
                if ($customer->platform_id && $customer->bot->platform == Bot::TELEGRAM) {
                    Yii::$app->tm->customer = $customer;
                    Yii::$app->tm->platformId = $customer->platform_id;
                    $TAdmin = new TAdmin();
                    $TAdmin->mainMenu($text);
                }
                if ($customer->platform_id && $customer->bot->platform == Bot::VIBER) {
                    Yii::$app->vb->customer = $customer;
                    Yii::$app->vb->platformId = $customer->platform_id;
                    $VAdmin = new VAdmin();
                    $VAdmin->keyboard($text, $VAdmin->mainMenuKeyboard());
                }
            } catch (\Exception $e) {
                BotLogger::save_input($e->getMessage(), __METHOD__);
                continue;
            }
            if ($x == 30) {
                $x = 0;
                sleep(1);
            }
            $x++;
        }
    }
}
