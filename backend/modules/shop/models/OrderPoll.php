<?php

namespace backend\modules\shop\models;

use src\behavior\Timestamp;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_order_shop_poll".
 *
 * @property int $id
 * @property int|null $order_id
 * @property int|null $poll_id
 * @property int|null $status
 * @property int|null $answer_first
 * @property int|null $answer_second
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Order $order
 * @property Poll $poll
 */
class OrderPoll extends ActiveRecord
{
    const STATUS_NOT_POLL = 0;
    const STATUS_FIRST_POLL = 1;
    const STATUS_SECOND_POLL = 2;

    const ANSWER_YES = 1;
    const ANSWER_NO = 0;

    public $onlyYes;

    public static function tableName(): string
    {
        return 'shop_order_shop_poll';
    }


    public function behaviors()
    {
        return [
            [
                'class' => Timestamp::class,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['order_id', 'poll_id', 'status', 'answer_first', 'answer_second'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['poll_id'], 'exist', 'skipOnError' => true, 'targetClass' => Poll::class, 'targetAttribute' => ['poll_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ №',
            'status' => 'Статус',
            'answer_first' => 'Ответ №1',
            'answer_second' => 'Ответ №2',
            'updated_at' => 'Обновлен'
        ];
    }

    public static function answersAll(): array
    {
        return [
            self::ANSWER_YES => 'Да',
            self::ANSWER_NO => 'Нет',
        ];
    }

    public static function answer($answer): string
    {
        switch ($answer) {
            case self::ANSWER_NO:
                $s = '<div class="badge badge-warning text-wrap">' . self::answersAll()[$answer] . '</div>';
                break;
            case self::ANSWER_YES:
            default:
                $s = '<div class="badge badge-success text-wrap">' . self::answersAll()[$answer] . '</div>';
                break;
        }
        return $s;
    }

    public static function statusesAll(): array
    {
        return [
            self::STATUS_NOT_POLL => 'Опрос еще не проводился',
            self::STATUS_FIRST_POLL => 'Проведен первый опрос',
            self::STATUS_SECOND_POLL => 'Проведен второй опрос',
        ];
    }

    public static function status($status): string
    {
        switch ($status) {
            case self::STATUS_NOT_POLL:
                $s = '<div class="badge badge-light text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_FIRST_POLL:
                $s = '<div class="badge badge-info text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_SECOND_POLL:
            default:
                $s = '<div class="badge badge-success text-wrap">' . self::statusesAll()[$status] . '</div>';
        }
        return $s;
    }

    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getPoll(): ActiveQuery
    {
        return $this->hasOne(Poll::class, ['id' => 'poll_id']);
    }

}
