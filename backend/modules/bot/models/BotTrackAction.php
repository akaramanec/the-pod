<?php

namespace backend\modules\bot\models;

use backend\modules\customer\models\Customer;
use Yii;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $data
 */
class BotTrackAction extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'bot_track_action';
    }

    public function rules()
    {
        return [
            [['customer_id', 'data'], 'required'],
            [['customer_id'], 'integer'],
            [['data'], 'safe'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    public static function saveAction()
    {
        $t = new self();
        $t->customer_id = Yii::$app->tm->customer->id;
        $t->data = Yii::$app->tm->data;
        $t->save();
    }
}
