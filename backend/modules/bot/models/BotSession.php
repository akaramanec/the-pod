<?php

namespace backend\modules\bot\models;

use backend\modules\customer\models\Customer;
use common\helpers\DieAndDumpHelper;
use Yii;

/**
 * @property int $customer_id
 * @property string $platform
 * @property string $name
 * @property string $data
 */
class BotSession extends \yii\db\ActiveRecord
{

    public $setPlatform;
    public $customerId;

    public static function tableName()
    {
        return 'bot_session';
    }

    public function rules()
    {
        return [
            [['customer_id', 'platform', 'name'], 'required'],
            [['customer_id'], 'integer'],
            [['platform'], 'string'],
            [['data'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'trim'],
            [['customer_id', 'platform', 'name'], 'unique', 'targetAttribute' => ['customer_id', 'platform', 'name']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function setByName($name, $data)
    {
        $session = self::find()
            ->where(['customer_id' => $this->customerId])
            ->andWhere(['platform' => $this->setPlatform])
            ->andWhere(['name' => $name])
            ->limit(1)->one();
        if ($session === null) {
            $session = new self();
            $session->customer_id = $this->customerId;
            $session->platform = $this->setPlatform;
            $session->name = $name;
        }
        $session->data = $data;
        $session->save();
        return $session;
    }

    public function getByName($name)
    {
        return self::find()
            ->where(['customer_id' => $this->customerId])
            ->andWhere(['platform' => $this->setPlatform])
            ->andWhere(['name' => $name])
            ->limit(1)->one();
    }

    public function getCustomerSession()
    {
        return self::find()
            ->where(['customer_id' => $this->customerId])
            ->andWhere(['platform' => $this->setPlatform])
            ->andWhere(['not', ['name' => 'common']])
            ->all();
    }

    public function del($name)
    {
        return self::deleteAll(['customer_id' => $this->customerId, 'platform' => $this->setPlatform, 'name' => $name]);
    }

    public function delAll()
    {
        return self::deleteAll(['customer_id' => $this->customerId, 'platform' => $this->setPlatform]);
    }

    public function set($name, $value)
    {
        return $this->setByName($name, [$name => $value]);
    }

    public function getSessionMessageId($name)
    {
        $model = $this->getByName($name);
        return $model ? $model->data['messageId'] : null;
    }

    public function get($name)
    {
        $model = $this->getByName($name);
        return $model ? $model->data[$name] : null;
    }

    public function saveMessageId($name, $messageId)
    {
        return $this->setByName($name, ['messageId' => $messageId]);
    }

    public function messageId($name)
    {
        $model = $this->getByName($name);
        return $model ? $model->data['messageId'] : null;
    }

    public function saveCommonRequest($request)
    {
        if (isset($request['ok']) && $request['ok'] === true && isset($request['result']['message_id'])) {
            (array)$data = $this->common();
            return $this->setByName('common', $this->data($request['result']['message_id'], $data));
        }
    }

    public function saveCommonMessageId($messageId)
    {
        if ($messageId) {
            (array)$data = $this->common();
            return $this->setByName('common', $this->data($messageId, $data));
        }
    }

    public function common()
    {
        $model = $this->getByName('common');
        return $model ? $model->data : ['message_id' => []];
    }

    private function data($message_id, $data, $mode = 'unique')
    {
        if ($mode == 'unique') {
            $data['message_id'][] = $message_id;
            $data = array_unique($data['message_id']);
            return ['message_id' => $data];
        }
        if ($mode == 'exclude') {
            if (($key = array_search($message_id, $data['message_id'])) !== false) {
                unset($data['message_id'][$key]);
            } else {
                $data['message_id'][] = $message_id;
            }
            return $data;
        }
    }
}
