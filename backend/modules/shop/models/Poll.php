<?php

namespace backend\modules\shop\models;

use backend\modules\admin\models\AuthAdmin;
use src\behavior\Timestamp;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_poll".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $question
 * @property int|null $updated_by
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read ActiveQuery $editor
 * @property-read ActiveQuery[] $orderPolls
 */
class Poll extends ActiveRecord
{
    const AFTER_ORDER_SUCCESS = 'afterOrderSuccess';

    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;

    public static function tableName(): string
    {
        return 'shop_poll';
    }

    public static function status($status): string
    {
        switch ($status) {
            case self::STATUS_ACTIVE:
                $s = '<div class="badge badge-success text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_INACTIVE:
                $s = '<div class="badge badge-danger text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
        }
        return $s;
    }

    public static function statusesAll(): array
    {
        return [
            self::STATUS_INACTIVE => 'Не активен',
            self::STATUS_ACTIVE => 'Активен',
        ];
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => Timestamp::class,
            ]
        ];
    }

    public function rules(): array
    {
        return [
            [['question'], 'string'],
            [['first_send_after', 'second_send_after'], 'string', 'max' => 10],
            [['updated_by'], 'integer'],
            [['question', 'first_send_after', 'second_send_after', 'updated_by', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'question' => 'Вопрос',
            'updated_by' => 'Редактировал',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
        ];
    }

    public function beforeSave($insert): bool
    {
        $this->updated_by = (int)Yii::$app->user->id;
        return parent::beforeSave($insert);
    }

    public function getEditor(): ActiveQuery
    {
        return $this->hasOne(AuthAdmin::class, ['id' => 'updated_by']);
    }

    public function getOrderPolls(): ActiveQuery
    {
        return $this->hasMany(OrderPoll::class, ['poll_id' => 'id']);
    }

    public function beforeUpdate(): void
    {
        $this->updated_by = Yii::$app->user->id;
    }
}
