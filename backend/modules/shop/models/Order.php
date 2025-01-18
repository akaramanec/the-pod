<?php

namespace backend\modules\shop\models;

use backend\modules\admin\models\AuthAdmin;
use backend\modules\admin\models\AuthLogger;
use backend\modules\bot\models\Bot;
use backend\modules\bot\models\Logger;
use backend\modules\bot\src\ApiProduct;
use backend\modules\bot\telegram\TCommon;
use backend\modules\bot\viber\VCommon;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\form\CustomerForm;
use backend\modules\system\models\Fondy;
use backend\modules\system\models\Interkassa;
use frontend\models\OrderCustomer;
use src\behavior\AfterSuccessPollBehavior;
use src\behavior\OrderPaymentBonusesBlogger;
use src\behavior\OrderStatusAndSourceUpdate;
use src\behavior\Timestamp;
use src\helpers\Common;
use Yii;

/**
 *
 * @property mixed $delivery
 * @property-read mixed $interkassa
 * @property-read mixed $np
 * @property-read mixed $mod
 * @property-read mixed $manager
 * @property-read mixed $fondyPay
 * @property-read mixed $orderItem
 * @property-read mixed $moysklad
 * @property-read mixed $botCustomer
 * @property-read mixed $payBlogger
 * @property-read mixed $fondy
 * @property-read mixed $orderCustomer
 * @property int $id [int(10) unsigned]
 * @property int $customer_id [int(10) unsigned]
 * @property bool $payment_method [tinyint(3) unsigned]
 * @property string $cache_sum_total [decimal(10,2)]
 * @property bool $status [tinyint(4)]
 * @property int $manager_id [int(11)]
 * @property int $source [int(11)]
 * @property string $created_at [datetime]
 * @property string $updated_at [datetime]
 * @property int $success_at [timestamp]
 * @property string $comment
 * @property string $address
 * @property string $blogger_bonus [decimal(10,2)]
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_NEW_TELEGRAM = 2;
    const STATUS_NEW_VIBER = 3;
    const STATUS_NEW_SITE = 4;
    const STATUS_CLOSE_CANCELED = 5;
    const STATUS_IN_WORK = 6;
    const STATUS_CLOSE_SUCCESS = 7;
    const STATUS_NEW_ADMIN = 8;
    const STATUS_DELETE = 9;

    const STATUS_IN_PROCESSING = 10;
    const STATUS_CONFIRMED = 11;
    const STATUS_UNCONFIRMED = 12;
    const STATUS_TEST = 13;
    const STATUS_CONFIRMED_AND_VIEWED = 14;

    const PAYMENT_METHOD_UNKNOWN = 1;
    const PAYMENT_METHOD_UPON_RECEIPT = 2;
    const PAYMENT_METHOD_PAY_ONLINE_NEW = 3;
    const PAYMENT_METHOD_PAY_ONLINE = 4;
    const PAYMENT_METHOD_PAY_TO_CARD = 5;
    const PAYMENT_METHOD_PAY_BY_BLOGGER_BONUS = 6;

    const SOURCE_TELEGRAM = 1;
    const SOURCE_VIBER = 2;
    const SOURCE_SITE = 3;
    const SOURCE_ADMIN = 4;
    const SOURCE_NON = 5;

    public $oldStatus;
    /** @var Customer|OrderCustomer */
    public $customer;

    public static function tableName()
    {
        return 'shop_order';
    }

    public function behaviors()
    {
        return [
            [
                'class' => Timestamp::class,
            ],
            [
                'class' => OrderStatusAndSourceUpdate::class
            ],
            [
                'class' => AfterSuccessPollBehavior::class
            ],
            [
                'class' => OrderPaymentBonusesBlogger::class
            ],
        ];
    }

    public function rules()
    {
        return [
            [['customer_id', 'payment_method'], 'required'],
            [['status'], 'default', 'value' => self::STATUS_NEW],
            [['payment_method'], 'default', 'value' => self::PAYMENT_METHOD_UNKNOWN],
            [['cache_sum_total'], 'default', 'value' => 0],
            [['customer_id', 'status', 'payment_method', 'source'], 'integer'],
            [['cache_sum_total', 'blogger_bonus'], 'number'],
            [['comment', 'address'], 'string', 'max' => 2000],
            [['comment', 'address'], 'trim'],
            [['delivery'], 'string', 'max' => 50],
            [['created_at', 'updated_at', 'delivery', 'manager_id', 'source'], 'safe'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№',
            'customer_id' => 'Заказчик',
            'status' => 'Статус',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'payment_method' => 'Оплата',
            'cache_sum_total' => 'Сумма',
            'comment' => 'Комментарий',
            'address' => 'Адрес',
            'manager_id' => 'Менеджер',
            'source' => 'Источник',
            'delivery' => 'Доставка'
        ];
    }

    public function afterFind()
    {
        $this->oldStatus = $this->status;
        if ($this->customer_id == Customer::CUSTOMER_SITE) {
            $this->customer = $this->orderCustomer;
        } else {
            $this->customer = $this->botCustomer;
        }
    }

    public function getBotCustomer()
    {
        return $this->hasOne(CustomerForm::class, ['id' => 'customer_id']);
    }

    public function getOrderCustomer()
    {
        return $this->hasOne(OrderCustomer::class, ['order_id' => 'id']);
    }

    public function getManager()
    {
        return $this->hasOne(AuthAdmin::class, ['id' => 'manager_id']);
    }

    public function getNp()
    {
        return $this->hasOne(OrderNp::class, ['order_id' => 'id']);
    }

    public function getInterkassa()
    {
        return $this->hasOne(Interkassa::class, ['order_id' => 'id']);
    }

    public function getPayBlogger()
    {
        return $this->hasOne(OrderPayBlogger::class, ['order_id' => 'id']);
    }

    public function getMoysklad()
    {
        return $this->hasOne(OrderMoysklad::class, ['order_id' => 'id']);
    }

    public function getDelivery()
    {
        return $this->hasOne(Delivery::class, ['slug' => 'delivery']);
    }

    public function getOrderItem()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id'])->indexBy('mod_id');
    }

    public function getFondy()
    {
        return $this->hasMany(Fondy::class, ['order_id' => 'id']);
    }

    public function getFondyPay()
    {
        return $this->hasOne(Fondy::class, ['order_id' => 'id'])->andWhere(['status' => Fondy::STATUS_PAY]);
    }

    public function getMod()
    {
        return $this->hasMany(ProductMod::class, ['id' => 'mod_id'])
            ->viaTable('shop_order_item', ['order_id' => 'id'])
            ->with(['product']);
    }

    public function customerFullName()
    {
        return $this->customer->last_name . ' ' . $this->customer->first_name;
    }

    public function customerFullNameParent()
    {
        if (isset($this->botCustomer->parent)) {
            return $this->botCustomer->parent->last_name . ' ' . $this->botCustomer->parent->first_name;
        }
    }

    public static function status($status)
    {
        switch ($status) {
            case self::STATUS_NEW:
            case self::STATUS_NEW_TELEGRAM:
            case self::STATUS_NEW_VIBER:
            case self::STATUS_NEW_SITE:
            case self::STATUS_NEW_ADMIN:
                $s = '<div class="badge badge-success text-wrap">' . self::statusesAll()[self::STATUS_NEW] . '</div>';
                break;
            case self::STATUS_CLOSE_CANCELED:
                $s = '<div class="badge badge-primary text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_IN_WORK:
                $s = '<div class="badge badge-warning text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_IN_PROCESSING:
                $s = '<div class="badge badge-warning text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_TEST:
                    $s = '<div class="badge badge-dark text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_CONFIRMED:
                $s = '<div class="badge badge-info text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_CONFIRMED_AND_VIEWED:
                $s = '<div class="badge badge-info text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_UNCONFIRMED:
                $s = '<div class="badge badge-secondary text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_CLOSE_SUCCESS:
            default:
                $s = '<div class="badge badge-light text-wrap">' . self::statusesAll()[$status] . '</div>';
        }
        return $s;
    }

    public static function statusesAll()
    {
        return [
//            self::STATUS_NEW_TELEGRAM => 'Новый Telegram',
//            self::STATUS_NEW_VIBER => 'Новый Viber',
//            self::STATUS_NEW_ADMIN => 'Новый Admin',
            self::STATUS_NEW => 'Новый',
            self::STATUS_CLOSE_CANCELED => 'Отменен',
            self::STATUS_IN_WORK => 'В работе',
            self::STATUS_CLOSE_SUCCESS => 'Завершен',
            self::STATUS_IN_PROCESSING => "В обработкe",
            self::STATUS_CONFIRMED => "Подтверждён (новый)",
            self::STATUS_CONFIRMED_AND_VIEWED => "Подтверждён",
            self::STATUS_UNCONFIRMED => "Не подтверждён",
            self::STATUS_TEST => 'Тест'
        ];
    }

    public static function paymentsForOrders(): array
    {
        return [
            self::PAYMENT_METHOD_UPON_RECEIPT => 'Оплата при получении',
            self::PAYMENT_METHOD_PAY_ONLINE => 'Онлайн оплата',
            self::PAYMENT_METHOD_PAY_TO_CARD => 'Оплата картой',
            self::PAYMENT_METHOD_PAY_BY_BLOGGER_BONUS => 'Оплата картой',
        ];
    }

    public static function statusesNew($withNew = false): array
    {
        $newStatuses = [
            self::STATUS_NEW_TELEGRAM => 'Новый Telegram',
            self::STATUS_NEW_VIBER => 'Новый Viber',
            self::STATUS_NEW_ADMIN => 'Новый Admin',
            self::STATUS_NEW_SITE => 'Новый SITE'
        ];
        if ($withNew) {
            $newStatuses[self::STATUS_NEW] = 'Новый';
        }
        return $newStatuses;
    }

    public static function statusPaymentMethod($status)
    {
        switch ($status) {
            case self::PAYMENT_METHOD_UPON_RECEIPT:
                $s = '<div class="badge badge-success text-wrap">' . self::statusesPaymentMethod()[$status] . '</div>';
                break;
            case self::PAYMENT_METHOD_PAY_ONLINE:
                $s = '<div class="badge badge-primary text-wrap">' . self::statusesPaymentMethod()[$status] . '</div>';
                break;
            case self::PAYMENT_METHOD_PAY_ONLINE_NEW:
                $s = '<div class="badge badge-primary text-wrap">' . self::statusesPaymentMethod()[$status] . '</div>';
                break;
            case self::PAYMENT_METHOD_PAY_TO_CARD:
                $s = '<div class="badge badge-warning text-wrap">' . self::statusesPaymentMethod()[$status] . '</div>';
                break;
            case self::PAYMENT_METHOD_PAY_BY_BLOGGER_BONUS:
                $s = '<div class="badge badge-info text-wrap">' . self::statusesPaymentMethod()[$status] . '</div>';
                break;
            case self::PAYMENT_METHOD_UNKNOWN:
            default:
                $s = '<div class="badge badge-default text-wrap">Неизвестен</div>';
                break;
        }
        return $s;
    }

    public static function statusesPaymentMethod()
    {
        return [
            self::PAYMENT_METHOD_UPON_RECEIPT => 'При получении',
            self::PAYMENT_METHOD_PAY_ONLINE => 'Оплата online',
            self::PAYMENT_METHOD_PAY_ONLINE_NEW => 'Оплата online (Не оплачен)',
            self::PAYMENT_METHOD_PAY_TO_CARD => 'Оплата на карту',
            self::PAYMENT_METHOD_PAY_BY_BLOGGER_BONUS => 'Оплата бонусами'
        ];
    }

    public static function source($date, $source)
    {
        $s = "<span>$date</span>";
        switch ($source) {
            case self::SOURCE_TELEGRAM:
                $s .= '<div class="badge badge-info text-wrap">' . self::sources()[$source] . '</div>';
                break;
            case self::SOURCE_VIBER:
                $s .= '<div class="badge badge-light text-wrap">' . self::sources()[$source] . '</div>';
                break;
            case self::SOURCE_SITE:
                $s .= '<div class="badge badge-success text-wrap">' . self::sources()[$source] . '</div>';
                break;
            case self::SOURCE_ADMIN:
                $s .= '<div class="badge badge-warning text-wrap">' . self::sources()[$source] . '</div>';
                break;
            case self::SOURCE_NON:
                $s .= '<div class="badge badge-danger text-wrap">' . self::sources()[$source] . '</div>';
                break;
            case null:
                $s .= '<div class="badge badge-danger text-wrap">' . self::sources()[Order::SOURCE_NON] . '</div>';
                break;
        }
        return $s;
    }

    public static function sources()
    {
        return [
            self::SOURCE_TELEGRAM => 'Telegram',
            self::SOURCE_VIBER => 'Viber',
            self::SOURCE_SITE => 'Сайт',
            self::SOURCE_ADMIN => 'Админ панель',
            self::SOURCE_NON => 'Без истоника',
        ];
    }

    public static function deliveries()
    {
        return [
            Delivery::DELIVERY_NP => 'Новая почта',
            Delivery::COURIER_DELIVERY => 'Курьер',
            Delivery::PICKUP => 'Самовывоз',
        ];
    }

    public static function delivery($deliveryType)
    {
        switch ($deliveryType) {
            case Delivery::DELIVERY_NP:
                $s = '<div class="badge badge-warning text-wrap">' . self::deliveries()[$deliveryType] . '</div>';
                break;
            case Delivery::COURIER_DELIVERY:
                $s = '<div class="badge badge-info text-wrap">' . self::deliveries()[$deliveryType] . '</div>';
                break;
            case Delivery::PICKUP:
            default:
                $s = '<div class="badge badge-success text-wrap">' . self::deliveries()[$deliveryType] . '</div>';
                break;
        }
        return $s;
    }

    public function actionByStatus()
    {
        if (!$this->manager_id) {
            $this->manager_id = Yii::$app->user->id;
            $newStatuses = array_keys(self::statusesNew(true));
            if (in_array($this->status, $newStatuses)) {
                $this->status = self::STATUS_IN_PROCESSING;
                switch ($this->botCustomer->bot->platform) {
                    case Bot::TELEGRAM:
                        $this->sendConfirmedMessageTelegram();
                        break;
                    case Bot::VIBER:
                        $this->sendConfirmedMessageViber();
                        break;
                }
            }
            $this->save(false);
        }

        if (
            $this->status == self::STATUS_CONFIRMED
            && $this->manager_id == \Yii::$app->user->id
        ) {
            $this->status = $this::STATUS_CONFIRMED_AND_VIEWED;
            $this->save(false);
        }
    }

    public function actionInWork()
    {
        if ($this->oldStatus != $this->status && $this->status == Order::STATUS_IN_WORK) {
            $api = new ApiProduct();
            $api->subtractQtyProduct($this);
        }
    }

    public function actionCloseSuccess()
    {
        if ($this->oldStatus != $this->status && $this->status == Order::STATUS_CLOSE_SUCCESS) {
            AuthLogger::saveModel(['actionCloseSuccess', 'status' => $this->status]);
        }
    }

    public static function timeUrl($action, $order)
    {
        return '/' . $action . '/' . self::timeId($order);
    }

    public static function timeId($order)
    {
        return $order->id . '-' . strtotime($order->created_at);
    }


    public static function setNew($customer_id)
    {
        if ($customer_id) {
            $order = self::find()
                ->where(['customer_id' => $customer_id])
                ->andWhere(['status' => self::STATUS_NEW])
                ->limit(1)->one();
            if ($order === null) {
                $order = self::create($customer_id);
                OrderNp::setNew($order->id);
            }
            return $order;
        }
    }

    public static function create($customer_id)
    {
        $order = new self();
        $order->customer_id = $customer_id;
        $order->payment_method = self::PAYMENT_METHOD_UNKNOWN;
        $order->save(false);
        return $order;
    }

    public function paidOnline()
    {
        if ($this->payment_method == self::PAYMENT_METHOD_PAY_ONLINE) {
            if (isset($this->interkassa->status_callback['ik_inv_st']) && $this->interkassa->status_callback['ik_inv_st'] == 'success') {
                return $this->interkassa->status_callback['ik_ps_price'];
            }
        }
    }

    /**
     * Check is Delivery - NP && Payment - UponReceipt
     * @return bool
     */
    public function isNpUponReceipt(): bool
    {
        return (!empty($this->payment_method)
            && !empty($this->delivery)
            && $this->payment_method === Order::PAYMENT_METHOD_UPON_RECEIPT
            && $this->delivery === Delivery::DELIVERY_NP
        );
    }

    public function clearItems()
    {
        OrderItem::deleteAll(['order_id' => $this->id]);
    }

    private function sendConfirmedMessageViber()
    {
        $botSender = new VCommon();
        \Yii::$app->vb->platformId = $this->owner->botCustomer->platform_id;
        $text = str_replace(['{orderId}'], $this->owner->id, $botSender->text('confirmedMessage'));
        $textYes = '<font color="#ffffff"><b>' . Common::str("Да", 0, 95) . '</b></font>';
        $textYNo = '<font color="#ffffff"><b>' . Common::str("Нет", 0, 95) . '</b></font>';
        $keyboard[] = $botSender->buttonImgText($textYes, '/img/vb/empty-min.jpg', [
            'action' => 'VOrder_confirmedOrder',
            'o_id' => $this->owner->id,
            's_id' => $this->owner::STATUS_CONFIRMED
        ]);
        $keyboard[] = $botSender->buttonImgText($textYNo, '/img/vb/empty-min.jpg', [
            'action' => 'VOrder_confirmedOrder',
            'o_id' => $this->owner->id,
            's_id' => $this->owner::STATUS_UNCONFIRMED
        ]);
        $botSender->keyboard($text, $keyboard);
    }

    private function sendConfirmedMessageTelegram()
    {
        $botSender = new TCommon();
        \Yii::$app->tm->platformId = $this->owner->botCustomer->platform_id;

        $buttons[] = [
            [
                'text' => 'Да',
                'callback_data' => json_encode([
                    'action' => '/TOrder_confirmedOrder',
                    'o_id' => $this->owner->id,
                    's_id' => $this->owner::STATUS_CONFIRMED
                ])
            ],
            [
                'text' => 'Нет',
                'callback_data' => json_encode([
                    'action' => '/TOrder_confirmedOrder',
                    'o_id' => $this->owner->id,
                    's_id' => $this->owner::STATUS_UNCONFIRMED
                ])
            ],
        ];
        $text = str_replace(['{orderId}'], $this->owner->id, $botSender->text('confirmedMessage'));

        $botSender->button($text, $buttons);
    }
}
