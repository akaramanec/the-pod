<?php

namespace backend\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;


class AuthItem extends \yii\db\ActiveRecord
{
    const ADMIN = 'admin';
    const CUSTOMER = 'customer';
    const MANAGER = 'manager';
    const MAIN_ADMIN = 'main_admin';
    const DEV = 'dev';

    public static function tableName()
    {
        return 'auth_item';
    }

    public static function rolesAll()
    {
        return ArrayHelper::map(self::find()->where(['type' => 1])->select(['name'])->asArray()->all(), 'name','name');
    }

    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
//            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Description',
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
    }

    public function getParents()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
    }

    public static function getRolesAsString($roles)
    {
        $rolesStr = '';
        foreach ($roles as $key => $role) {
            if ($key != 0) {
                $rolesStr .= ', ';
            }
            $rolesStr .= $role->item_name;
        }
        return $rolesStr;
    }
}
