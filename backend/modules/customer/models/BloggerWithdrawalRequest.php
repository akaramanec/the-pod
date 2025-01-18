<?php

namespace backend\modules\customer\models;

use Yii;
use backend\modules\customer\models\Customer as BotCustomer;

/**
 * This is the model class for table "blogger_withdrawal_request".
 *
 * @property int $id
 * @property int $bot_customer_id
 * @property int $bot_customer_card_id
 * @property float $sum
 * @property int|null $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BotCustomerCard $botCustomerCard
 * @property BotCustomer $botCustomer
 */
class BloggerWithdrawalRequest extends \yii\db\ActiveRecord
{
    const STATUS_CREATING = 0;
    const STATUS_NEW = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_FINISH = 3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blogger_withdrawal_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bot_customer_id'], 'required'],
            [['bot_customer_id', 'bot_customer_card_id', 'status'], 'integer'],
            [['sum'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['bot_customer_card_id'], 'exist', 'skipOnError' => true, 'targetClass' => BotCustomerCard::className(), 'targetAttribute' => ['bot_customer_card_id' => 'id']],
            [['bot_customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => BotCustomer::className(), 'targetAttribute' => ['bot_customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bot_customer_id' => 'Bot Customer ID',
            'bot_customer_card_id' => 'Bot Customer Card ID',
            'sum' => 'Sum',
            'status' => 'Status',
            'bot_customer_first_name' => 'Имя',
            'bot_customer_last_name' => 'Фамилия',
            'bot_customer_username' => 'Username',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[BotCustomerCard]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBotCustomerCard()
    {
        return $this->hasOne(BotCustomerCard::className(), ['id' => 'bot_customer_card_id']);
    }

    /**
     * Gets query for [[BotCustomer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBotCustomer()
    {
        return $this->hasOne(BotCustomer::className(), ['id' => 'bot_customer_id']);
    }
}
