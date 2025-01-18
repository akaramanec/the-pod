<?php
namespace backend\modules\admin\models;


class AuthAdminCustomer extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'auth_admin_customer';
    }

    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['company', 'position'], 'string', 'max' => 255],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => AuthAdmin::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'admin_customer_id' => 'Admin Customer ID',
            'id' => 'Admin ID',
            'company' => 'Компанія',
            'position' => 'Посада',
        ];
    }

    public function getAdmin()
    {
        return $this->hasOne(AuthAdmin::className(), ['id' => 'id']);
    }
}
