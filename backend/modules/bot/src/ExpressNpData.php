<?php

namespace backend\modules\bot\src;

use backend\modules\shop\models\Order;
use Exception;
use frontend\models\cart\Cart;
use frontend\models\cart\CartData;
use src\helpers\Date;
use Yii;

/**
 * @property Order $_order
 * @property Cart $_cart
 * @property ApiNp $_apiNp
 * @property SenderNp $_sender
 */
class ExpressNpData
{
    private $_order;
    private $_cart;
    private $_apiNp;
    private $_sender;

    public $NewAddress = '1';
    public $PayerType = 'Recipient';
    public $PaymentMethod;
    public $CargoType = 'Parcel';
    public $VolumeGeneral;
    public $Weight;
    public $ServiceType;
    public $SeatsAmount;
    public $Description;
    public $Cost;

    public $Sender;
    public $CitySender;
    public $SenderAddress;
    public $ContactSender;
    public $SendersPhone;

    public $RecipientCityName;
    public $RecipientArea;
    public $RecipientAreaRegions;
    public $RecipientAddressName;
    public $RecipientHouse = '';
    public $RecipientFlat = '';
    public $RecipientName;
    public $RecipientsPhone;
    public $RecipientType = 'PrivatePerson';
    public $DateTime;
    public $BackwardDeliveryData;

    public function __construct($order, $apiNp)
    {
        $this->_apiNp = $apiNp;
        $this->_sender = new SenderNp($this->_apiNp);
        $this->_order = $order;
        $this->_cart = new Cart();
        $this->_cart->build(new CartData($this->_order));

        $this->setPaymentMethod();
        $this->setWeight();
        $this->setServiceType();
        $this->setSeatsAmount();
        $this->setDescription();
        $this->setCost();

        $this->setSender();
        $this->setSenderAddress();
        $this->setSendersPhone();
        $this->setCitySender();
        $this->setContactSender();

        $this->setRecipientCityName();
        $this->setRecipientArea();
        $this->setRecipientAreaRegions();
        $this->setRecipientAddressName();
        $this->setRecipientName();
        $this->setRecipientsPhone();

        $this->setDateTime();
        $this->setBackwardDeliveryData();
    }

    public function setPaymentMethod()
    {
        $this->PaymentMethod = 'Cash';
    }

    public function setBackwardDeliveryData()
    {
        switch ($this->_order->payment_method) {
            case Order::PAYMENT_METHOD_UPON_RECEIPT:
                $this->BackwardDeliveryData[] = [
                    'PayerType' => 'Recipient',
                    'CargoType' => 'Money',
                    'RedeliveryString' => $this->_cart->cacheSumTotal
                ];
                break;
            case Order::PAYMENT_METHOD_PAY_ONLINE:
                $this->BackwardDeliveryData = '';
                break;
        }
    }

    public function setWeight()
    {
        if (!$this->_order->np->weight) {
            throw new Exception('Необходимо заполнить вес');
        }
        $this->Weight = $this->_order->np->weight;
    }

    public function setServiceType()
    {
        if (!$this->_order->np->service_type) {
            throw new Exception('Необходимо заполнить технологию доставки');
        }
        $this->ServiceType = $this->_order->np->service_type;
    }

    public function setSeatsAmount()
    {
        if (!$this->_order->np->seats_amount) {
            throw new Exception('Необходимо заполнить количество мест отправления');
        }
        $this->SeatsAmount = $this->_order->np->seats_amount;
    }

    public function setDescription()
    {
        if (!$this->_order->np->description) {
            throw new Exception('Необходимо заполнить количество мест отправления');
        }
        $this->Description = $this->_order->np->description;
    }

    public function setCost()
    {
        if (!$this->_cart->sumTotal) {
            throw new Exception('Необходимо установить общую стоимость');
        }
        $this->Cost = $this->_cart->cacheSumTotal;
    }

    public function setDateTime()
    {
        if (!$this->_order->np->departure_date) {
            throw new Exception('Необходимо заполнить дату отправления');
        }
        $this->DateTime = Date::format_date($this->_order->np->departure_date);
    }
    //$getCounterparties = $counterpartyNp->getCounterparties('Sender');
    public function setSender()
    {
        $this->Sender = $this->_sender->CounterpartyRef;
    }

    //"item_branch": {"Ref": "7b422fc3-e1b8-11e3-8c4a-0050568002cf",
    public function setSenderAddress()
    {
        $this->SenderAddress = Yii::$app->common->settingNp['item_branch_ref'];
    }

    public function setSendersPhone()
    {
        $this->SendersPhone = $this->_sender->ContactPersonsPhones;
    }

    //$counterpartyNp->getCounterpartyContactPersons('b8ee8750-d959-11ea-8513-b88303659df5')  setSender()
    public function setContactSender()
    {
        $this->ContactSender = $this->_sender->ContactPersonsRef;
    }

    //'delivery_city' => '8d5a980d-391c-11dd-90d9-001a92567626'    'city' => 'м. Київ, Київська обл.'
    public function setCitySender()
    {
        $this->CitySender = Yii::$app->common->settingNp['delivery_city_ref'];
    }

    //'delivery_city' => '3c67c750-a440-11dd-bcd8-001d92f78697'   'city' => 'м. Броди, Бродівський р-н, Львівська обл.'
    public function setRecipientCityName()
    {
        if (isset($this->_order->np->item_city['MainDescription'])) {
            return $this->RecipientCityName = $this->_order->np->item_city['MainDescription'];
        }
        throw new Exception('Необходимо заполнить setRecipientCityName');
    }

    public function setRecipientArea()
    {
        if (isset($this->_order->np->item_city['Area'])) {
            return $this->RecipientArea = $this->_order->np->item_city['Area'];
        }
        throw new Exception('Необходимо заполнить setRecipientArea');
    }

    public function setRecipientAreaRegions()
    {
        if (isset($this->_order->np->item_city['Region'])) {
            return $this->RecipientAreaRegions = $this->_order->np->item_city['Region'];
        }
        throw new Exception('Необходимо заполнить setRecipientAreaRegions');
    }

    //'branch_ref' => '511fd010-e1c2-11e3-8c4a-0050568002cf' 'city' => 'м. Броди,
    public function setRecipientAddressName()
    {
        if (isset($this->_order->np->item_branch['Number'])) {
            return $this->RecipientAddressName = $this->_order->np->item_branch['Number'];
        }
        throw new Exception('Необходимо заполнить setRecipientAddressName');
    }

    public function setRecipientName()
    {
        if (!$this->_order->customer->last_name && !$this->_order->customer->first_name) {
            throw new Exception('Необходимо заполнить ФИО');
        }
        $this->RecipientName = $this->_order->customer->last_name . ' ' . $this->_order->customer->first_name;
    }

    public function setRecipientsPhone()
    {
        if (!$this->_order->customer->phone) {
            throw new Exception('Необходимо заполнить телефон');
        }
        $this->RecipientsPhone = $this->_order->customer->phone;
    }


}
