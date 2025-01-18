<?php

namespace backend\modules\customer\models;

/**
 * @property int $id
 * @property int $newsletter_id
 * @property int $customer_id
 * @property int $status
 * @property string $data [json]
 *
 * @property Newsletter $newsletter
 * @property Customer $customer
 */
class NewsletterMessages extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'newsletter_messages';
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => Newsletter::STATUS_SEND],
            [['status', 'newsletter_id', 'customer_id'], 'required'],
            [['newsletter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Newsletter::class, 'targetAttribute' => ['newsletter_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function getNewsletter()
    {
        return $this->hasOne(Newsletter::class, ['id' => 'newsletter_id']);
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}