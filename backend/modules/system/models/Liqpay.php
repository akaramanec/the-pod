<?php

namespace backend\modules\system\models;

use Yii;

/**
 * This is the model class for table "pay_liqpay".
 *
 * @property int $id
 * @property string $test_public_key
 * @property string $test_private_key
 * @property string $public_key
 * @property string $private_key
 * @property int $status
 */
class Liqpay extends \yii\db\ActiveRecord
{
    const STATUS_TEST = 1;
    const STATUS_PROD = 2;

    public static function tableName()
    {
        return 'pay_liqpay';
    }

    public function rules()
    {
        return [
            [['test_public_key', 'test_private_key', 'public_key', 'private_key', 'status'], 'required'],
            [['status'], 'integer'],
            [['test_public_key', 'test_private_key', 'public_key', 'private_key'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'test_public_key' => Yii::t('app', 'Test Public Key'),
            'test_private_key' => Yii::t('app', 'Test Private Key'),
            'public_key' => Yii::t('app', 'Public Key'),
            'private_key' => Yii::t('app', 'Private Key'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public static function statusesAll()
    {
        return [
            self::STATUS_TEST => 'Test',
            self::STATUS_PROD => 'Prod',
        ];
    }
}
