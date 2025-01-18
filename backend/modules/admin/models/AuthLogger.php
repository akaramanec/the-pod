<?php

namespace backend\modules\admin\models;

use Yii;

/**
 * @property int $admin_id
 * @property string $controller
 * @property string $action
 * @property string|null $request
 * @property string $created_at
 */
class AuthLogger extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'auth_logger';
    }

    public static function getDb()
    {
        return Yii::$app->get('loggerDb');
    }

    public function rules()
    {
        return [
            [['admin_id'], 'required'],
            [['admin_id'], 'integer'],
            [['data', 'request', 'created_at'], 'safe'],
            [['controller', 'action'], 'string', 'max' => 255],
            [['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuthAdmin::class, 'targetAttribute' => ['admin_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin_id' => 'Admin ID',
            'controller' => 'Controller',
            'action' => 'Action',
            'request' => 'Request',
            'created_at' => 'Created At',
        ];
    }

    public function getAdmin()
    {
        return $this->hasOne(AuthAdmin::class, ['id' => 'admin_id']);
    }

    public function afterFind()
    {
//        $this->request =  print_r($this->request);
    }

    public static function saveModel($data = [])
    {
        $logger = new self();
        $logger->admin_id = Yii::$app->user->id;
        $logger->controller = Yii::$app->controller->id;
        $logger->action = Yii::$app->controller->action->id;
        $logger->created_at = Yii::$app->common->datetimeNow;
        $logger->data = $data;
        $logger->request = [
            'get' => $_GET,
            'post' => $_POST
        ];
        $logger->insert();
    }

    public static function saveNp($data = [])
    {
        $logger = new self();
        $logger->admin_id = 1;
        $logger->controller = 'np';
        $logger->action = 'check-status-en-np';
        $logger->created_at = Yii::$app->common->datetimeNow;
        $logger->data = $data;
        $logger->request = [
        ];
        $logger->insert();
    }
}
