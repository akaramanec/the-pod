<?php

namespace blog\models;

use backend\modules\customer\models\Customer;
use Yii;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $data
 */
class CustomerLinkReferral extends \yii\db\ActiveRecord
{
    public $modId;
    public $productCode;

    public static function tableName()
    {
        return 'bot_customer_link_referral';
    }

    public function rules()
    {
        return [
            [['customer_id', 'data'], 'required'],
            [['customer_id'], 'integer'],
            [['data'], 'safe'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'name' => 'Name'
        ];
    }

    public function afterFind()
    {
        $this->setData();
    }

    public function setData()
    {
        if (isset($this->data['modId'])) {
            $this->modId = $this->data['modId'];
        }
        if (isset($this->data['productCode'])) {
            $this->productCode = $this->data['productCode'];
        }
    }

    public function buildData()
    {
        $this->data = [
            'modId' => $this->modId,
            'productCode' => $this->productCode,
        ];
    }

    public function checkExistModId()
    {
        foreach (self::find()->where(['customer_id' => Yii::$app->user->identity->id])->all() as $item) {
            if ($this->modId == $item->modId) {
                throw new \Exception('Такая ссылка уже существует');
            }
        }
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}
