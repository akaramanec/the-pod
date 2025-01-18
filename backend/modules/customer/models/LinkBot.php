<?php

namespace backend\modules\customer\models;

use backend\modules\shop\models\Order;
use Yii;

/**
 * @property int $id
 * @property string $name
 */
class LinkBot extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'pod_link_bot';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'trim'],
            ['name', 'filter', 'filter' => function ($value) {
                return mb_strtolower($value, 'UTF-8');
            }],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название'
        ];
    }

    public function getLinkBotLink()
    {
        return $this->hasMany(LinkBotLink::class, ['link_bot_id' => 'id']);
    }

    public function getCustomer()
    {
        return $this->hasMany(Customer::class, ['id' => 'customer_id'])->viaTable('pod_link_bot_link', ['link_bot_id' => 'id']);
    }

    public function countCustomer()
    {
        return LinkBotLink::find()->where(['link_bot_id' => $this->id])->count();
    }

    public function countOrder()
    {
        return self::find()
            ->alias('linkBot')
            ->where(['linkBotLink.link_bot_id' => $this->id])
            ->andWhere(['order.status' => Order::STATUS_CLOSE_SUCCESS])
            ->joinWith(['customer.order AS order', 'linkBotLink AS linkBotLink'])
            ->groupBy('order.customer_id')
            ->count();
    }
}
