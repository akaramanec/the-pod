<?php

namespace blog\models;

use backend\modules\customer\models\Customer;
use src\email\BloggerNotification;
use src\validators\Is;
use Yii;
use yii\helpers\Inflector;

/**
 * @property int $customer_id
 * @property string $username
 * @property string|null $pass
 * @property int|null $percent_level_1
 * @property int|null $percent_level_2
 * @property int|null $percent_level_3
 * @property string|null $password
 * @property string $cache [json]
 */
class CustomerBlog extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'bot_customer_blog';
    }

    public function rules()
    {
        return [
            [['customer_id', 'percent_level_1', 'percent_level_2', 'percent_level_3', 'pass'], 'required'],
            [['customer_id', 'percent_level_1', 'percent_level_2', 'percent_level_3'], 'integer'],
            [['percent_level_1'], 'integer', 'min' => 0, 'max' => 100],
            [['percent_level_2'], 'integer', 'min' => 0, 'max' => 100],
            [['percent_level_3'], 'integer', 'min' => 0, 'max' => 100],
            [['username', 'pass', 'password'], 'string', 'max' => 255],
            [['cache'], 'safe'],
            [['username'], 'unique'],
            [['customer_id'], 'unique'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'customer_id' => 'Customer ID',
            'username' => 'Username',
            'pass' => 'Пароль',
            'percent_level_1' => 'Процент level 1',
            'percent_level_2' => 'Процент level 2',
            'percent_level_3' => 'Процент level 3',
            'password' => 'Password',
        ];
    }

    public $customerLevel = [];
    public $customerId = [];
    public $customerCount = 0;
    public $orders = [];
    public $ordersIdNoPay = [];
    public $ordersCount = 0;
    public $sumTotalOrders = 0;
    public $sumTotalPayed = 0;
    public $sumDebt = 0;
    public $checkSumDebt;
    public $payAll = [];
    public $lvl3Need = false;

    public function afterFind()
    {
        if (isset($this->cache['customerLevel'])) {
            $this->customerLevel = $this->cache['customerLevel'];
        }
        if (isset($this->cache['customerId'])) {
            $this->customerId = $this->cache['customerId'];
        }
        if (isset($this->cache['customerCount'])) {
            $this->customerCount = $this->cache['customerCount'];
        }
        if (isset($this->cache['orders'])) {
            $this->orders = $this->cache['orders'];
        }
        if (isset($this->cache['ordersIdNoPay'])) {
            $this->ordersIdNoPay = $this->cache['ordersIdNoPay'];
        }
        if (isset($this->cache['ordersCount'])) {
            $this->ordersCount = $this->cache['ordersCount'];
        }
        if (isset($this->cache['sumTotalOrders'])) {
            $this->sumTotalOrders = $this->cache['sumTotalOrders'];
        }
        if (isset($this->cache['sumTotalPayed'])) {
            $this->sumTotalPayed = $this->cache['sumTotalPayed'];
        }
        if (isset($this->cache['sumDebt'])) {
            $this->sumDebt = $this->cache['sumDebt'];
        }
        if (isset($this->cache['checkSumDebt'])) {
            $this->checkSumDebt = $this->cache['checkSumDebt'];
        }
        if (isset($this->cache['payAll'])) {
            $this->payAll = $this->cache['payAll'];
        }
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public static function getModel($customer)
    {
        if (!empty($customer->blog)) {
            return $customer->blog;
        }
        return new self();
    }

    public static function saveModel(CustomerBlog $self, $customer)
    {
        $bloggerExist = Customer::find()
            ->where(['phone' => $customer->phone])
            ->andWhere(['blogger' => Customer::BLOGGER_TRUE])
            ->limit(1)->count();
        if ($bloggerExist > 1) {
            throw new \Exception('Этот номер телефона уже является блогером');
        }
        if ($self->isNewRecord) {
            $self->customer_id = $customer->id;
            $self->percent_level_1 = 10;
            $self->percent_level_2 = 5;
            if ($self->lvl3Need) {
                $self->percent_level_3 = 1;
            }
            $self->username = Inflector::slug(Customer::fullName($self->customer)) . '-' . Yii::$app->security->generateRandomString(2);
            if (!$self->save(false)) {
                Is::errors($self->errors);
            }
            return true;
        }

        $self->load(Yii::$app->request->post());
        if (!$self->username) {
            $self->username = Inflector::slug(Customer::fullName($self->customer));
        }
        $self->password = Yii::$app->security->generatePasswordHash($self->pass);
        if (!$self->save()) {
            Is::errors($self->errors);
        }

        if (Yii::$app->request->post('blogger') == 'send') {
            try {
                new BloggerNotification($self);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
            }
        }
    }


}
