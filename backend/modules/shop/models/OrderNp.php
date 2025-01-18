<?php

namespace backend\modules\shop\models;

use backend\modules\bot\models\Bot;
use backend\modules\bot\src\DocumentNp;
use backend\modules\bot\src\ExpressNpData;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\viber\VAdmin;
use DateTime;
use src\helpers\Date;
use src\validators\Is;
use src\validators\OnlyRussianLetters;
use Yii;
use yii\helpers\BaseFileHelper;

class OrderNp extends \yii\db\ActiveRecord
{

    public $city;
    public $item_city;
    public $branch;
    public $item_branch;
    public $document;
    public $documentData;
    public $printDocumentUrl;

    public static function tableName()
    {
        return 'shop_order_np';
    }

    public function rules()
    {
        return [
            [['description', 'city', 'branch'], 'trim'],
            [['service_type'], 'default', 'value' => 'WarehouseWarehouse'],
            [['order_id', 'departure_date', 'description', 'weight', 'seats_amount'], 'required'],
            [['order_id', 'seats_amount'], 'integer'],
            [['data'], 'default', 'value' => []],
            [['data', 'departure_date'], 'safe'],
            [['weight'], 'number'],
            [['description'], 'string'],
            [['description'], OnlyRussianLetters::class],
            [['city', 'branch', 'service_type'], 'string', 'max' => 255],
            [['order_id'], 'unique'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    public static function primaryKey()
    {
        return ['order_id'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['new_bot'] = ['order_id', 'data', 'service_type', 'weight', 'seats_amount', 'description'];
        $scenarios['new_order_site'] = ['order_id', 'data', 'service_type', 'city', 'branch'];
        $scenarios['ttn'] = ['order_id', 'data', 'description', 'departure_date', 'weight', 'seats_amount'];
        return $scenarios;
    }

    public static function setNew($order_id)
    {
        $orderNp = new self();
        $orderNp->scenario = 'new_bot';
        $orderNp->order_id = $order_id;
        $orderNp->service_type = 'WarehouseWarehouse';
        $orderNp->weight = 0.5;
        $orderNp->seats_amount = 1;
        $orderNp->description = 'Электронные устройства';
        $orderNp->buildData();
        $orderNp->save(false);
        return $orderNp;
    }

    public function saveNewFromSite($order_id)
    {
        $this->setDataFromSession();
        $this->buildData();
        $this->scenario = 'new_bot';
        $this->order_id = $order_id;
        $this->service_type = 'WarehouseWarehouse';
        $this->weight = 0.5;
        $this->seats_amount = 1;
        $this->description = 'Электронные устройства';
        if (!$this->save()) {
            Is::errors($this->errors);
        }
        return $this;
    }

    public function attributeLabels()
    {
        return [
            'city' => 'Город',
            'branch' => 'Отделение',
            'weight' => 'Вес',
            'service_type' => 'Технологии доставки',
            'seats_amount' => 'Количество мест отправления',
            'description' => 'Доп. описания',
            'departure_date' => 'Дата отправления',
        ];
    }

    public function setDataFromSession()
    {
        if (isset($_SESSION['item_city']) && $_SESSION['item_city'] && isset($_SESSION['item_branch']) && $_SESSION['item_branch']) {
            $this->item_city = $_SESSION['item_city'];
            $this->item_branch = $_SESSION['item_branch'];

            $this->city = $this->data['item_city']['Present'];
            $this->branch = $this->data['item_branch']['DescriptionRu'];
            return true;
        }
    }

    public function afterFind()
    {
        if (isset($this->data['item_city'])) {
            $this->item_city = $this->data['item_city'];
            $this->city = $this->data['item_city']['Present'];
        }
        if (isset($this->data['item_branch'])) {
            $this->item_branch = $this->data['item_branch'];
            $this->branch = $this->data['item_branch']['DescriptionRu'];
        }
        if (isset($this->data['document'])) {
            $this->document = $this->data['document'];
            if (isset($this->document['data'][0])) {
                $this->documentData = $this->document['data'][0];
                $this->printDocumentUrl = 'https://my.novaposhta.ua/orders/printDocument/orders[]/' . $this->documentData['IntDocNumber'] . '/type/pdf/apiKey/' . Yii::$app->common->settingNp['key'];
            }
        }
    }

    public function dateFormatForSave()
    {
        $this->departure_date = Date::datetime_converter($this->departure_date);
    }

    public function buildData()
    {
        $this->data = [
            'item_city' => $this->item_city,
            'item_branch' => $this->item_branch,
            'document' => $this->document
        ];
    }

    public function checkComparisonWithCurrentDate()
    {
        if (new DateTime(Date::date_now()) <= new DateTime($this->departure_date)) {
            return true;
        }
    }

    public function comparisonWithCurrentDate()
    {
        if (!$this->checkComparisonWithCurrentDate()) {
            throw new \Exception('Вы не можете выбрать дату меньше текущей');
        }
    }

    public static function serviceType()
    {
        return [
            'WarehouseWarehouse' => 'Відділення-Відділення',
            'DoorsDoors' => 'Адреса-Адреса',
            'DoorsWarehouse' => 'Адреса-Відділення',
            'WarehouseDoors' => 'Відділення-Адреса'
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function internetDocumentSave($apiNp)
    {
        if (Yii::$app->request->post('internetDocument') == 'save') {
            $documentNp = new DocumentNp($apiNp);
            if ($this->document && isset($this->document['data'][0]['Ref'])) {
                $documentNp->delete($this->document['data'][0]['Ref']);
                $this->document = null;
                $this->buildData();
                $this->save();
            }
            $this->document = $documentNp->internetDocumentSave(new ExpressNpData($this->order, $apiNp));
            Yii::$app->common->error = $this->document;
            if ($this->document->success == true) {
                $this->buildData();
                $this->save();

                $orderNp = self::findOne($this->order_id);
                $name = $orderNp->documentData['IntDocNumber'] . '.pdf';
                $path = Yii::getAlias('@frontend/web/uploads/ttn/' . $orderNp->order->customer->id . '/');
                $fullPath = $path . $name;
                BaseFileHelper::removeDirectory($path);
                BaseFileHelper::createDirectory($path);
                file_put_contents($fullPath, file_get_contents($orderNp->printDocumentUrl));
                if (!is_file($fullPath)) {
                    throw new \Exception('ТТН сформировать не удалось');
                }
                $url = Yii::$app->params['homeUrl'] . '/uploads/ttn/' . $orderNp->order->customer->id . '/' . $name;

                if (isset($orderNp->order->customer->bot->platform)) {
                    if ($orderNp->order->customer->bot->platform == Bot::TELEGRAM) {
                        Yii::$app->tm->customer = $orderNp->order->customer;
                        Yii::$app->tm->platformId = $orderNp->order->customer->platform_id;
                        $admin = new TAdmin();
                        $admin->sendTtn($url);
                    }
                    if ($orderNp->order->customer->bot->platform == Bot::VIBER) {
                        Yii::$app->vb->customer = $orderNp->order->customer;
                        Yii::$app->vb->platformId = $orderNp->order->customer->platform_id;
                        $admin = new VAdmin();
                        $admin->sendTtn($url);
                    }
                }
                return true;
            }
            throw new \Exception('ТТН сформировать не удалось');
        }
    }


}
