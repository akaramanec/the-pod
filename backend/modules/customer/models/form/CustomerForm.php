<?php

namespace backend\modules\customer\models\form;

use backend\modules\bot\models\Bot;
use backend\modules\customer\models\Customer;
use src\behavior\CapitalLetters;
use src\behavior\Timestamp;
use src\validators\CheckSpace;
use src\validators\OnlyRussianLetters;

class CustomerForm extends Customer
{
    public function behaviors()
    {
        return [
            [
                'class' => Timestamp::class,
            ],
            [
                'class' => CapitalLetters::class,
                'fields' => ['first_name', 'last_name'],
            ],
        ];
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'username'], 'trim'],
            ['phone', 'filter', 'filter' => function ($value) {
                $pattern_int = '/[^0-9]/';
                return preg_replace($pattern_int, '', $value);
            }],
            [['first_name', 'last_name'], CheckSpace::class],
            [['first_name', 'last_name'], OnlyRussianLetters::class],
            [['bot_id', 'parent_id', 'status'], 'integer'],
            [['discount'], 'integer', 'min' => 0, 'max' => 100],
            [['discount'], 'default', 'value' => 0],
            [['blogger'], 'default', 'value' => self::BLOGGER_FALSE],
            [['bot_id', 'platform_id'], 'required'],
            [['updated_at', 'created_at', 'tags'], 'safe'],
            [['platform_id', 'email', 'command'], 'string', 'max' => 100],
            ['email', 'email'],
            ['email', 'filter', 'filter' => function ($value) {
                return mb_strtolower($value, 'UTF-8');
            }],
            [['first_name', 'last_name', 'username'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 30],
            [['img'], 'string', 'max' => 40],
            [['platform_id'], 'unique'],
            [['black_list', 'regular_customer'], 'boolean'],
            [['bot_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bot::class, 'targetAttribute' => ['bot_id' => 'id']],
        ];
    }
}
