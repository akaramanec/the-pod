<?php

namespace backend\modules\notification\models\form;

use backend\modules\notification\models\enum\NotificationSettingEnum;
use backend\modules\notification\models\service\NotificationService;
use yii\base\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $text
 * @property string $img
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property array $settings
 */
class NotificationForm extends Model
{
    public $id;
    public $name;
    public $text;
    public $img;

    public $settings;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'text'], 'required'],
            [['text'], 'string'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['img'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['settings'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'text' => 'Текст',
            'img' => 'Img'
        ];
    }

    /**
     * [type => label]
     * @return array
     */
    public function settingsFormField(): array
    {
        return [
            NotificationSettingEnum::NOT_ACTIVE_TIME => 'Время неактивности пользователя'
        ];
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        return NotificationService::saveByForm($this);
    }

}