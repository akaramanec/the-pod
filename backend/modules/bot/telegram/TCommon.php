<?php

namespace backend\modules\bot\telegram;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotPlaceholder;
use backend\modules\bot\models\BotSession;
use backend\modules\bot\src\Menu;
use backend\modules\bot\src\traits\CartTrait;
use backend\modules\customer\models\ClickStatistic;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderItem;
use backend\modules\system\models\Setting;
use frontend\models\cart\Cart;
use frontend\models\cart\CartData;
use src\helpers\DieAndDumpHelper;
use src\services\data\CustomerData;
use Yii;

/**
 * @property \frontend\models\cart\Cart $cart
 * @property Order $order
 * @property BotSession $session
 */
class TCommon extends TBaseCommon
{
    use CartTrait;

    public $menu;
    public $cart;
    public $order;
    public $session;
    public $mod_id;
    protected $text = '';
    protected $_itemCart;
    protected $_orderItem;

    public function __construct()
    {
        $this->setOrder();
        $this->menu = new Menu();
        if (isset(Yii::$app->tm->customer->id)) {
            $this->startSession(Yii::$app->tm->customer->id);
        }
        if (isset(Yii::$app->tm->data->mod_id)) {
            $this->mod_id = Yii::$app->tm->data->mod_id;
        }
    }

    public function startSession($customer_id)
    {
        $this->session = new BotSession();
        $this->session->setPlatform = Bot::TELEGRAM;
        $this->session->customerId = $customer_id;
    }

    public function saveSessionMessageId($name)
    {
        if (isset($this->request['result']['message_id'])) {
            $this->session->saveMessageId($name, $this->request['result']['message_id']);
        }
    }

    public function start()
    {
        $messageId = $this->session->messageId('mainMessageId');
        $this->deleteMessageByMessageId($messageId);
        $this->delCommon();
        $this->clearSession();
        $this->delMainMessageId();
        $this->saveCommandNull();
        $this->session->saveCommonMessageId(Yii::$app->tm->messageId);
        $this->auth();
        $this->sendPhoto($this->text('showButtons'), Yii::$app->params['dataUrl'] . '/img/unknown-menu.png');
        $this->mainMenu($this->text('start'));
    }

    public function startContinue()
    {
        $messageId = $this->session->messageId('mainMessageId');
        $this->deleteMessageByMessageId($messageId);
        $this->delCommon();
        $this->clearSession();
        $this->delMainMessageId();
        $this->saveCommandNull();
        $this->session->saveCommonMessageId(Yii::$app->tm->messageId);
        $this->auth();
        $this->mainMenu($this->text('startContinue'));
    }

    public function unknown()
    {
        $this->auth();
        $this->deleteMessage();
        $this->sendPhoto($this->text('unknown'), Yii::$app->params['dataUrl'] . '/img/unknown-menu.png');
        $this->session->saveCommonRequest($this->request);
    }

    public function mainMenu($text = null)
    {
        $this->keyboard($text, $this->keyboardMainMenu());
    }

    public function keyboardMainMenu()
    {
        return [
            [
                ["text" => $this->menu->catalogFlavor],
                ["text" => $this->menu->cart],
            ],
            [
                ["text" => $this->menu->faq],
                ["text" => $this->menu->freeTesting],
            ],
            [
                ["text" => $this->menu->dialOperator],
                ["text" => $this->menu->cabinet]
            ],
        ];
    }

    public function dialOperatorUrl()
    {
        $setting = Setting::listValue('contact');
        return Yii::$app->params['homeUrl'] . '/home/to-call?' . http_build_query([
                'phone' => $setting['phone'],
            ]);
    }

    public function dialOperator()
    {
        $this->session->del('selectedProduct');
        $setting = Setting::listValue('contact');
        $button[] = [["text" => $this->text('dialOperator'), "url" => $this->dialOperatorUrl()]];
        $this->delCommon();

        $this->button($setting['phone'], $button);
    }

    public function setMainMessageId()
    {
        return Yii::$app->tm->messageId = $this->session->messageId('mainMessageId');
    }

    public function checkCustomer()
    {
        return Yii::$app->tm->customer->checkAuthAllCustomer();
    }

    public function auth()
    {
        if (Yii::$app->tm->customer->status == Customer::STATUS_ACTIVE || Yii::$app->tm->customer->status == Customer::STATUS_SUBSCRIBED) {
            return true;
        }
        switch (Yii::$app->tm->customer->status) {
            case Customer::STATUS_NEW:
            case Customer::STATUS_INACTIVE:
                $this->sendMessage($this->text('startRegistration'));
                $this->saveClick(ClickStatistic::START);
                Yii::$app->tm->action('/TAuth_agePolicy');
                exit(__METHOD__ . __LINE__);
            case Customer::STATUS_EDIT:
                Yii::$app->tm->action('/TAuth_dataConfirmation');
                $this->keyboard($this->text('dataConfirmationRegistration'), [
                    [
                        ["text" => $this->menu->dataConfirmationSave]
                    ]
                ]);
                $this->session->saveCommonRequest($this->request);
                exit(__METHOD__ . __LINE__);
            case Customer::STATUS_UNSUBSCRIBED:
                Yii::$app->tm->customer->status = Customer::STATUS_SUBSCRIBED;
                Yii::$app->tm->customer->save();
                Yii::$app->tm->action('/start');
                exit(__METHOD__ . __LINE__);
            default:
                exit(__METHOD__ . __LINE__);
        }
    }

    public function setOrder()
    {
        if (isset(Yii::$app->tm->customer->id)) {
            $this->order = Order::setNew(Yii::$app->tm->customer->id);
        }
    }

    public function setOrderItem()
    {
        if (($mod_id = $this->getSessionModId()) !== null) {
            $this->_orderItem = OrderItem::findOne(['mod_id' => $mod_id, 'order_id' => $this->order->id]);
        }
    }

    public function setCart()
    {
        $this->cart = new Cart();
        $this->cart->build(new CartData($this->order));
        $this->cart->sumPayByBonus = $this->session->get('SumPayOrderByBonus') ?: 0;
        if (($mod_id = $this->getSessionModId()) !== null && isset($this->cart->items[$mod_id])) {
            $this->_itemCart = $this->cart->items[$mod_id];
        }
    }

    public function getSessionModId()
    {
        if (isset(Yii::$app->tm->data->mod_id)) {
            return Yii::$app->tm->data->mod_id;
        }
        if (($selectedProduct = $this->session->get('selectedProduct')) !== null) {
            return $selectedProduct;
        }
        return null;
    }

    public function delMainMessageId()
    {
        $this->deleteMessageByMessageId($this->session->messageId('mainMessageId'));
        $this->session->del('mainMessageId');
    }

    public function setTextProduct($productData)
    {
        $placeholder = [
            '{{image}}' => '<a href="' . $productData['img'] . '"> </a>',
            '{{name}}' => $productData['name'],
            '{{productText}}' => $productData['text'],
        ];

        if ($this->cart->data->discountPercent) {
            $placeholder['{{price}}'] = '<strike>' . (int)$productData['price'] . ' ' . Cart::CURRENCY . '</strike>';
            $placeholder['{{percent}}'] = $this->cart->data->discountPercent;
            $placeholder['{{discountPrice}}'] = $this->cart->percent($productData['price']) . ' ' . Cart::CURRENCY;
        } else {
            $placeholder['{{price}}'] = $productData['price']. ' ' . Cart::CURRENCY;
        }
        $this->text = strtr($this->text('textProduct'), $placeholder);
    }

    public static function strikethroughText($text)
    {
        $response = '';
        foreach (str_split($text) as $item) {
            if ($item == '.') {
                $item = ',';
            }
            $response .= $item . '̶';
        }
        return $response . ' ₴';
    }

    public function infoProductQtyPrice()
    {
        if ($this->cart->data->discountPercent) {
            $priceItemProductTotal = $this->cart->percent($this->_itemCart['priceItemProductTotal']);
        } else {
            $priceItemProductTotal = $this->_itemCart['priceItemProductTotal'];
        }
        return $this->_orderItem->qty . 'шт. - ' . $this->cart->showPrice($priceItemProductTotal);
    }

    public function delCommon()
    {
        $data = $this->session->common();
        if ($data['message_id']) {
            foreach ($data['message_id'] as $message_id) {
                $this->deleteMessageByMessageId($message_id);
            }
            $this->session->setByName('common', ['message_id' => []]);
        }
    }

    public function clearSession()
    {
        $sessionsData = $this->session->getCustomerSession();
        foreach ($sessionsData as $sessionsDatum) {
            $sessionsDatum->delete();
        }
    }

    public function textCustomer($customer, $title = '')
    {
        $customerData = new CustomerData($customer);
        $placeholder = BotPlaceholder::findOne(['slug' => 'customer_all']);
        $text = '';
        if ($title) {
            $text .= $title . PHP_EOL;
        }
        $text .= BotPlaceholder::placeholder($placeholder->text, $customerData->data) . PHP_EOL;
        return $text;
    }

    public function saveClick($click)
    {
        ClickStatistic::saveClick($click, Yii::$app->tm->customer->id);
    }

    public function check()
    {
        return '✅ ';
    }

    public function help()
    {
        return Yii::$app->tm->action('/TFaq_listFaq');
    }

    public function freeTasting()
    {
        $this->session->del('selectedProduct');
        $button[] = [["text" => $this->text('dialManager'), "url" => $this->dialOperatorUrl()]];
        $this->delCommon();
        $this->button($this->text('freeTastingDescription'), $button);
        $this->saveSessionMessageId('mainMessageId');
    }
}
