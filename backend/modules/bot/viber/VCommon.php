<?php

namespace backend\modules\bot\viber;

use backend\modules\bot\models\Bot;
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
use Viber\Api\Keyboard\Button;
use Yii;

/**
 * @property Cart $cart
 * @property Order $order
 * @property BotSession $session
 */
class VCommon extends VBaseCommon
{
    use CartTrait;

    public $menu;
    public $cart;
    public $order;
    public $session;
    public $mod_id;
    protected $keyboard = [];
    protected $text = '';
    protected $_itemCart;
    protected $_orderItem;

    public function __construct()
    {
        parent::__construct();
        $this->setOrder();
        $this->menu = new Menu();
        if (isset(Yii::$app->vb->customer->id)) {
            $this->startSession(Yii::$app->vb->customer->id);
        }
        if (isset(Yii::$app->vb->data->mod_id)) {
            $this->mod_id = Yii::$app->vb->data->mod_id;
        }
    }

    public function startSession($customer_id)
    {
        $this->session = new BotSession();
        $this->session->setPlatform = Bot::VIBER;
        $this->session->customerId = $customer_id;
    }

    public function start()
    {
        $this->auth();
        $this->saveCommandNull();
        return $this->mainMenu();
    }

    public function unknown()
    {
        $this->auth();
        $this->saveCommandNull();
        if (isset(Yii::$app->vb->data->value)) {
            $v = explode(':', Yii::$app->vb->data->value);
            if (isset($v[0]) && ($v[0] == 'https' || $v[0] == 'http')) {
                exit('unknown https');
            }
            if (Yii::$app->vb->data->value == 'none') {
                exit('unknown none');
            }
        }
        $this->mainMenu($this->text('unknown'));
    }

    public function startRegistration()
    {
        switch (Yii::$app->vb->customer->status) {
            case Customer::STATUS_NEW:
                $button[] = $this->buttonImg('start', '/img/vb/start-min.jpg', ['action' => 'VRegistration_phone']);
                $this->keyboard($this->text('startRegistration'), $button);
                exit(__METHOD__ . __LINE__);
            case Customer::STATUS_INACTIVE:
                $button[] = $this->buttonImg('start', '/img/vb/start-min.jpg', ['action' => 'VRegistration_phone']);
                $this->keyboard($this->text('startRegistration'), $button);
                exit(__METHOD__ . __LINE__);
            case Customer::STATUS_EDIT:
                Yii::$app->vb->action('VRegistration_dataConfirmation');
                exit(__METHOD__ . __LINE__);
            default:
                exit(__METHOD__ . __LINE__);
        }
    }

    public function checkCustomer()
    {
        return Yii::$app->vb->customer->checkAuthAllCustomer();
    }

    public function auth()
    {
        if (Yii::$app->vb->customer->status == Customer::STATUS_ACTIVE || Yii::$app->vb->customer->status == Customer::STATUS_SUBSCRIBED) {
            return true;
        }
        $this->startRegistration();
    }

    public function setCart()
    {
        $this->cart = new Cart();
        $this->cart->build(new CartData($this->order));
        if (isset(Yii::$app->vb->data->mod_id) && isset($this->cart->items[Yii::$app->vb->data->mod_id])) {
            $this->_itemCart = $this->cart->items[Yii::$app->vb->data->mod_id];
        }
    }

    //{"action":"VCommon_mainMenu"}
    public function mainMenu($text = null)
    {
        if ($text === null) {
            $text = $this->text('mainMenuButton');
        }
        $this->auth();
        $this->saveCommandNull();
        $this->keyboard($text, $this->mainMenuKeyboard());
    }

    public function mainMenuKeyboard()
    {
        $keyboard[] = $this->buttonImg('Faq', '/img/vb/faq-min.jpg', ['action' => 'VFaq_listFaq'], 3);
        $keyboard[] = $this->buttonImg('Корзина', '/img/vb/korz_1-2-min.jpg', ['action' => 'VCart_menuCart'], 3);
        $keyboard[] = $this->buttonImg('Быстрый заказ', '/img/vb/fast_1-2.png', ['action' => 'VCatalog_branchQuickOrder'], 3);
        $keyboard[] = $this->buttonImg('Каталог', '/img/vb/katalog_1-2.png', ['action' => 'VCatalog_branchFlavor'], 3);
        $keyboard[] = $this->buttonUrlImg('dialOperator', '/img/vb/call-min.jpg', $this->dialOperatorUrl());
        return $keyboard;
    }

    public function dialOperatorUrl()
    {
        $setting = Setting::listValue('contact');
        return Yii::$app->params['homeUrl'] . '/home/to-call?' . http_build_query([
                'phone' => $setting['phone'],
            ]);
    }

    public function setTextProduct($productData)
    {
        $this->text .= $productData['name'] . PHP_EOL;
        $this->text .= $productData['text'];
        if ($this->cart->data->discountPercent) {
            $this->text .= 'Цена : ' . $productData['priceFormat'] . PHP_EOL;
            $this->text .= 'Цена cо скидкой ' . $this->cart->data->discountPercent . '%: ' . $this->cart->showPrice($this->cart->percent($productData['price']));
        } else {
            $this->text .= 'Цена : ' . $productData['priceFormat'];
        }
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

    public function buttonMainMenu()
    {
        return (new Button())
            ->setColumns(6)
            ->setActionType('reply')
            ->setSilent(true)
            ->setBgColor($this->mainBg)
            ->setBgMedia(Yii::$app->params['dataUrl'] . '/img/vb/glavnoe-min.jpg')
            ->setText($this->text('mainMenuButton'))
            ->setTextOpacity(0)
            ->setActionBody($this->encode(['action' => 'VCommon_mainMenu']));
    }

    public function buttonMainMenu4()
    {
        return (new Button())
            ->setColumns(4)
            ->setActionType('reply')
            ->setSilent(true)
            ->setBgColor($this->mainBg)
            ->setBgMedia(Yii::$app->params['dataUrl'] . '/img/vb/glavnoe_2-3-min.jpg')
            ->setText($this->text('mainMenuButton'))
            ->setTextOpacity(0)
            ->setActionBody($this->encode(['action' => 'VCommon_mainMenu']));
    }

    public function setOrderItem()
    {
        if (isset(Yii::$app->vb->data->mod_id)) {
            $this->_orderItem = OrderItem::findOne(['mod_id' => Yii::$app->vb->data->mod_id, 'order_id' => $this->order->id]);
        }
    }

    public function setOrder()
    {
        if (isset(Yii::$app->vb->customer->id)) {
            $this->order = Order::setNew(Yii::$app->vb->customer->id);
        }
    }

    public function textCustomer($customer, $head = '')
    {
        $text = '';
        if ($head) {
            $text .= $head . PHP_EOL;
        }
        if ($customer->phone) {
            $text .= 'Мой номер телефона: ' . $customer->phone;
        }
        return $text;
    }

    public function saveClick($click)
    {
        ClickStatistic::saveClick($click, Yii::$app->vb->customer->id);
    }

    public function check()
    {
        return '✅ ';
    }
}
