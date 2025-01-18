<?php

namespace backend\modules\customer\models;

use Yii;
use backend\modules\customer\models\Customer as BotCustomer;
/**
 * This is the model class for table "bot_customer_card".
 *
 * @property int $id
 * @property int $bot_customer_id
 * @property string $number
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BloggerWithdrawalRequest[] $bloggerWithdrawalRequests
 * @property BotCustomer $botCustomer
 */
class BotCustomerCard extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_customer_card';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bot_customer_id', 'number'], 'required'],
            [['bot_customer_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['number'], 'string', 'max' => 255],
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
            'number' => 'Number',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[BloggerWithdrawalRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBloggerWithdrawalRequests()
    {
        return $this->hasMany(BloggerWithdrawalRequest::className(), ['bot_customer_card_id' => 'id']);
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
