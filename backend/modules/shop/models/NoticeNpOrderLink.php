<?php

namespace backend\modules\shop\models;

use Yii;

/**
 * @property string $notice_np_name
 * @property-read Order $order
 * @property-read mixed $noticeNp
 * @property int $order_id
 */
class NoticeNpOrderLink extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_notice_np_order_link';
    }

    public static function findIssued(): array
    {
        return self::findAll(['notice_np_name' => NoticeNp::STATUS_ISSUED]);
    }

    public function rules()
    {
        return [
            [['notice_np_name', 'order_id'], 'required'],
            [['order_id'], 'integer'],
            [['notice_np_name'], 'string', 'max' => 255],
            [['notice_np_name', 'order_id'], 'unique', 'targetAttribute' => ['notice_np_name', 'order_id']],
            [['notice_np_name'], 'exist', 'skipOnError' => true, 'targetClass' => NoticeNp::class, 'targetAttribute' => ['notice_np_name' => 'name']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    public static function primaryKey()
    {
        return ['notice_np_name', 'order_id'];
    }

    public function getNoticeNp()
    {
        return $this->hasOne(NoticeNp::class, ['name' => 'notice_np_name']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
}
