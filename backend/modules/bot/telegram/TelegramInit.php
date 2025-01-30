<?php

namespace backend\modules\bot\telegram;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotMenu;
use backend\modules\bot\models\Logger;
use backend\modules\bot\src\ActionReflection;
use backend\modules\shop\models\ProductMod;
use src\helpers\Common;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\base\BaseObject;

class TelegramInit extends BaseObject
{
    public $bot;
    private $_input;
    private $_type;
    private $_platformId;
    private $_messageId;
    private $_customer;
    private $_data;

    public function __construct($config = [])
    {
        $this->bot = Bot::bot(Bot::TELEGRAM);
        parent::__construct($config);
    }

    public function run()
    {
        if (isset($this->_input->message->chat->id) && $this->_input->message->chat->id == Yii::$app->params['groupTmChatId']) {
            exit(__METHOD__);
        }
        $this->setType();
        $this->setCustomer(TCustomer::getOrSetByPlatformId());
        $this->setData();

        if (isset($this->_data->action)) {
            return $this->action($this->_data->action);
        } else {
            return true;
        }
    }

    public function action($action)
    {
        $a = new ActionReflection();
        $a->actionTelegram($action);
    }

    public function getPlatformId()
    {
        return $this->_platformId;
    }

    public function setPlatformId($platform_id)
    {
        $this->_platformId = $platform_id;
    }

    public function getMessageId()
    {
        return $this->_messageId;
    }

    public function setMessageId($message_id)
    {
        $this->_messageId = $message_id;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setType()
    {
        if (isset($this->_input->callback_query)) {
            $this->setPlatformId($this->_input->callback_query->message->chat->id);
            $this->setMessageId($this->_input->callback_query->message->message_id);
            return $this->_type = 'callback_query';
        }

        if (isset($this->_input->message->contact)) {
            $this->setPlatformId($this->_input->message->chat->id);
            $this->setMessageId($this->_input->message->message_id);
            return $this->_type = 'contact';
        }

        if (isset($this->_input->message->entities[0])) {
            $this->setPlatformId($this->_input->message->chat->id);
            $this->setMessageId($this->_input->message->message_id);

            if ($this->_input->message->entities[0]->type == 'bot_command') {
                return $this->_type = 'bot_command';
            } else {
                return $this->_type = 'message';
            }
        }

        if (isset($this->_input->message)) {
            $this->setPlatformId($this->_input->message->chat->id);
            $this->setMessageId($this->_input->message->message_id);
            return $this->_type = 'message';
        }

        if (isset($this->_input->edited_message)) {
            $this->setPlatformId($this->_input->edited_message->chat->id);
            $this->setMessageId($this->_input->edited_message->message_id);
            return $this->_type = 'edited_message';
        }
        if (isset($this->_input->my_chat_member->new_chat_member->status) && $this->_input->my_chat_member->new_chat_member->status == 'kicked') {
            $this->setPlatformId($this->_input->my_chat_member->chat->id);
            return $this->_type = 'kicked';
        }
        if (!$this->_type) {
            exit(__METHOD__);
        }

        return null;
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
            case 'message':
                $action = null;
                $value = null;
                if (isset($this->_input->message->text)) {
                    $this->clearText();
                    if ($menuParser = BotMenu::command($this->_input->message->text)) {
                        Logger::commit($menuParser);
                        $common = new TBaseCommon();
                        $common->deleteMessage();
                        $action = $menuParser;
                    } elseif (isset($this->_customer->command)) {
                        $action = $this->_customer->command;
                    } elseif ($this->isSearchQuery()) {
                        $action = '/TCatalog_searchProductsInit';
                    }
                    $value = trim($this->_input->message->text);
                }
                if (!$action) {
                    $action = '/unknown';
                }
                $d = [
                    'action' => $action,
                    'value' => $value
                ];
                $this->_data = json_decode(json_encode($d));
                break;
            case 'callback_query':
                if (isset($this->_input->callback_query->data)) {
                    $this->_data = json_decode($this->_input->callback_query->data);
                }
                break;
            case 'contact':
                $d = [
                    'action' => '/TAuth_phoneSave',
                    'value' => Common::onlyInt($this->_input->message->contact->phone_number)
                ];
                $this->_data = json_decode(json_encode($d));
                break;
            case 'bot_command':
                if (isset($this->_input->message->text)) {
                    $command = explode(' ', $this->_input->message->text);
                    if (count($command) >= 2) {
                        $d = [
                            'action' => $command[0],
                            'value' => $command[1]
                        ];
                        $this->_data = json_decode(json_encode($d));
                        break;
                    }
                    $d = [
                        'action' => trim($this->_input->message->text),
                        'value' => null
                    ];
                    $this->_data = json_decode(json_encode($d));
                }
                break;
            case 'kicked':
                $this->_data = json_decode(json_encode(['action' => '/TAuth_unsubscribed']));
                break;
            default:
                $this->_data = json_decode(json_encode(['action' => '/TCommon_unknown']));
        }
    }

    public function clearText()
    {
        $clear = ['Каталог', 'Старт'];
        foreach ($clear as $item) {
            if (strripos($this->_input->message->text, $item) !== false) {
                return $this->_input->message->text = $item;
            }
        }
    }

    public function isSearchQuery(): bool
    {
        $searchResult = null;
        $searchWords = explode(',', $this->_input->message->text);
        foreach ($searchWords as $searchWord) {
            $searchResult = ProductMod::byName($searchWord) == [] ? null : ProductMod::byName($searchWord);
            if ($searchResult != null) {
                break;
            }
        }
        return $searchResult != null;
    }
}
