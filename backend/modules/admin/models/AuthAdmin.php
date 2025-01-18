<?php

namespace backend\modules\admin\models;

use backend\modules\media\models\ImgSave;
use src\behavior\AuthRole;
use src\behavior\CapitalLetters;
use src\behavior\Timestamp;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class AuthAdmin
 * @package backend\modules\admin\models
 *
 * @property int $id [int(10) unsigned]
 * @property string $surname [varchar(50)]
 * @property string $name [varchar(50)]
 * @property string $phone [varchar(20)]
 * @property string $email [varchar(255)]
 * @property string $auth_key [varchar(32)]
 * @property string $password [varchar(255)]
 * @property string $password_reset_token [varchar(255)]
 * @property bool $status [tinyint(4)]
 * @property string $created_at [datetime]
 * @property string $updated_at [datetime]
 * @property string $img [varchar(100)]
 */
class AuthAdmin extends \yii\db\ActiveRecord
{
    /** @var array */
    public $rolesArr;

    public static function tableName()
    {
        return 'auth_admin';
    }

    public function behaviors()
    {
        return [
            [
                'class' => AuthRole::class
            ],
            [
                'class' => Timestamp::class,
            ],
            [
                'class' => CapitalLetters::class,
                'fields' => ['surname', 'name'],
            ],
            [
                'class' => ImgSave::class,
                'entityImg' => ADMIN
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'auth_key', 'password'], 'required'],
            [['surname', 'name', 'email'], 'trim'],
            [['status'], 'integer'],
            [['created_at', 'updated_at', 'rolesArr'], 'safe'],
            [['surname', 'name'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 20],
            [['email', 'password', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['email'], 'unique'],
            [['phone'], 'unique'],
            [['password_reset_token'], 'unique'],
            ['status', 'in', 'range' => [Admin::STATUS_ACTIVE, Admin::STATUS_NOT_ACTIVE]],
            [['img'], 'string', 'max' => 100],
            [['mainImg'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'surname' => 'Фамилия',
            'name' => Yii::t('app', 'Name'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password' => Yii::t('app', 'Password'),
            'status' => Yii::t('app', 'Status'),
            'roles' => 'Роли',
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(AuthAdminCustomer::class, ['id' => 'id']);
    }

    public function getRoles(): ActiveQuery
    {
        return $this->hasMany(AuthAssignment::class, ['user_id' => 'id']);
    }

    public static function getById($id)
    {
        return self::find()->where(['id' => $id])->limit(1)->one();
    }

    public static function idNameAll(): array
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'surname');
    }

    public static function statusesAll(): array
    {
        return [
            Admin::STATUS_ACTIVE => 'Активен',
            Admin::STATUS_NOT_ACTIVE => 'Не активен',
        ];
    }

    public static function managersAll(): array
    {
        return ArrayHelper::map(self::find()->select(['id', 'surname'])->asArray()->all(), 'id', 'surname');
    }

    public static function status($status)
    {
        switch ($status) {
            case Admin::STATUS_ACTIVE:
                $s = 'Активен';
                break;
            case Admin::STATUS_NOT_ACTIVE:
                $s = 'Не активен';
                break;
        }
        return $s;
    }
}
