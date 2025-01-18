<?php

namespace backend\modules\bot\viber;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\models\BotSession;
use backend\modules\bot\src\ActionReflection;
use yii\base\BaseObject;

class ViberInit extends BaseObject
{
    public $bot;

    private $_input;
    private $_customer;
    private $_type;
    private $_platformId;
    private $_messageId;
    private $_data;

    public function __construct($config = [])
    {
        $this->bot = Bot::bot(Bot::VIBER);
        parent::__construct($config);
    }

    public function run()
    {
        $this->platformId();
        $this->setType();
        $this->setCustomer(VCustomer::getOrSetByPlatformId());
        $this->setData();
//        $this->checkMessageToken();
        if (isset($this->_data->action)) {
            return $this->action($this->_data->action);
        } else {
            return true;
        }
    }

    public function checkMessageToken()
    {
        $session = new BotSession();
        $session->setPlatform = Bot::VIBER;
        $session->customerId = $this->_customer->id;
        if ($this->_input->message_token == $session->get('message_token')) {
            exit('ViberInit checkMessageToken');
        } else {
            $session->set('message_token', $this->_input->message_token);
        }
    }

    public function action($action)
    {
        $a = new ActionReflection();
        $a->actionViber($action);
    }

    public function getPlatformId()
    {
        return $this->_platformId;
    }

    public function platformId()
    {
        if (isset($this->_input->sender->id)) {
            $this->setPlatformId($this->_input->sender->id);
        }
        if (isset($this->_input->user->id)) {
            $this->setPlatformId($this->_input->user->id);
        }
        if (isset($this->_input->user_id)) {
            $this->setPlatformId($this->_input->user_id);
        }
        if (!$this->_platformId) {
            exit('Не удалось создать platformId');
        }
    }

    public function setPlatformId($platformId)
    {
        $this->_platformId = $platformId;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setType()
    {
        if (isset($this->_input->event) && $this->_input->event == 'subscribed') {
            return $this->_type = 'subscribed';
        }
        if (isset($this->_input->type) && $this->_input->type == 'open') {
            return $this->_type = 'open';
        }
        if (isset($this->_input->message->contact)) {
            return $this->_type = 'contact';
        }
        if (isset($this->_input->message->type) && $this->_input->message->type == 'location') {
            return $this->_type = 'location';
        }
        if (isset($this->_input->message->type) && $this->_input->message->type == 'text') {
            return $this->_type = 'text';
        }
        if (isset($this->_input->event) && $this->_input->event == 'unsubscribed') {
            return $this->_type = 'unsubscribed';
        }
        if (!$this->_type) {
            exit('Не удалось создать Type');
        }
    }

    public function getCustomer()
    {
        return $this->_customer;
    }

    public function setCustomer($customer)
    {
        $this->_customer = $customer;
    }

    public function getInput()
    {
        return $this->_input;
    }

    public function setInput($input)
    {
        $this->_input = $input;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setData()
    {
        switch ($this->_type) {
            case 'text':
                if (isset($this->_input->message->text)) {
                    if ($this->isJson($this->_input->message->text)) {
                        $this->_data = json_decode($this->_input->message->text);
                        break;
                    }
                    $action = null;
                    if ($this->_customer->command) {
                        $action = $this->_customer->command;
                    }
                    if (!$action) {
                        $action = 'VCommon_unknown';
                    }
                    $d = [
                        'action' => $action,
                        'value' => trim($this->_input->message->text)
                    ];
                    $this->_data = json_decode(json_encode($d));
                }
                break;
            case 'open':
                if (isset($this->_input->context) && stripos($this->_input->context, '=') !== false) {
                    $context = explode(':', $this->_input->context);
                    $d = [];
                    foreach ($context as $item) {
                        $item = explode('=', $item);
                        $d[$item[0]] = $item[1];
                    }
                    $this->_data = json_decode(json_encode($d));
                    break;
                }
                $d = [
                    'action' => 'VCommon_start',
                ];
                $this->_data = json_decode(json_encode($d));
                break;
            case 'subscribed':
                $d = [
                    'action' => 'VCommon_start',
                ];
                $this->_data = json_decode(json_encode($d));
                break;
            case 'contact':
                $d = [
                    'action' => 'VRegistration_phoneSave',
                    'value' => $this->_input->message->contact->phone_number
                ];
                $this->_data = json_decode(json_encode($d));
                break;
            case 'location':
                $d = [
                    'action' => 'VCommon_unknown'
                ];
                $this->_data = json_decode(json_encode($d));
                break;
            default:
                $this->_data = json_decode(json_encode(['action' => 'VCommon_unknown']));
                BotLogger::save_input('No Set Data VInit', __METHOD__);
        }
    }

    public function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

}
