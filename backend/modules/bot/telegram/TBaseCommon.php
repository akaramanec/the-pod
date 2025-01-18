<?php

namespace backend\modules\bot\telegram;

use api\base\API;
use api\base\Request;
use backend\modules\bot\models\BotCommand;
use backend\modules\bot\models\BotLogger;
use backend\modules\customer\models\Customer;
use Yii;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;

class TBaseCommon
{
    public $request;

    public function sendMessage($text)
    {
        return $this->send([
            'method' => 'sendMessage',
            'chat_id' => Yii::$app->tm->platformId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }

    public function editMessageText($text)
    {
        $this->send([
            'method' => 'editMessageText',
            'chat_id' => Yii::$app->tm->platformId,
            'message_id' => Yii::$app->tm->messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
        if ($this->checkEdit()) {
            $this->sendMessage($text);
        }
    }

    public function sendCalendar($text, $button)
    {
        return $this->send([
            'method' => 'editMessageText',
            'chat_id' => Yii::$app->tm->platformId,
            'message_id' => Yii::$app->tm->messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $button
        ]);
    }

    public function editMessageTextReplyMarkupJson($text, $button)
    {
        return $this->send([
            'method' => 'editMessageText',
            'chat_id' => Yii::$app->tm->platformId,
            'message_id' => Yii::$app->tm->messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $button
        ]);
    }

    public function sendMessageByChatId($chat_id, $text)
    {
        return $this->send([
            'method' => 'sendMessage',
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public function button($text, $button)
    {
        return $this->send([
            'method' => 'sendMessage',
            'chat_id' => Yii::$app->tm->platformId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                "inline_keyboard" => $button
            ], true)
        ]);
    }

    public function edit($text, $button, $messageId)
    {
        $this->send([
            'method' => 'editMessageText',
            'chat_id' => Yii::$app->tm->platformId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                "inline_keyboard" => $button
            ], true)
        ]);
        if ($this->checkEdit()) {
            $this->button($text, $button);
        }
    }

    protected function checkEdit()
    {
        return $this->request['ok'] == false && $this->request['description'] == 'Bad Request: message to edit not found';
    }

    public function keyboard($text, $keyboard)
    {
        return $this->send([
            'method' => 'sendMessage',
            'chat_id' => Yii::$app->tm->platformId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                "keyboard" => $keyboard,
                'resize_keyboard' => true,
            ], true)
        ]);
    }

    public function keyboardEdit($text, $keyboard)
    {
        return $this->send([
            'method' => 'editMessageText',
            'chat_id' => Yii::$app->tm->platformId,
            'message_id' => Yii::$app->tm->messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                "keyboard" => $keyboard,
                'resize_keyboard' => true,
            ], true)
        ]);
    }

    public function editMessageReplyMarkup($button)
    {
        return $this->send([
            'method' => 'editMessageReplyMarkup',
            'chat_id' => Yii::$app->tm->platformId,
            'message_id' => Yii::$app->tm->messageId,
            'reply_markup' => json_encode([
                "inline_keyboard" => $button
            ], true)
        ]);
    }

    public function sendPhoto($text, $photo)
    {
        return $this->send([
            'method' => 'sendPhoto',
            'chat_id' => Yii::$app->tm->platformId,
            'photo' => $photo,
            'caption' => $text,
        ]);
    }

    public function editMessageMedia($text, $media, $button)
    {
        return $this->send([
            'method' => 'editMessageMedia',
            'chat_id' => Yii::$app->tm->platformId,
            'media' => $media,
            'message_id' => $text,
            'reply_markup' => json_encode([
                "inline_keyboard" => $button
            ], true)
        ]);
    }

    public function sendPhotoKeyboard($text, $photo, $button)
    {
        return $this->send([
            'method' => 'sendPhoto',
            'chat_id' => Yii::$app->tm->platformId,
            'photo' => $photo,
            'caption' => $text,
            'reply_markup' => json_encode([
                "inline_keyboard" => $button
            ], true)
        ]);
    }

    public function sendVideo($video, $text = '')
    {
        $params = [
            'method' => 'sendVideo',
            'chat_id' => Yii::$app->tm->platformId,
            'video' => $video,
        ];
        if ($text) {
            $params['caption'] = $text;
            $params['parse_mode'] = 'HTML';
        }
        $this->send($params);
    }

    public function contact($text, $button)
    {
        return $this->send([
            'method' => 'sendMessage',
            'chat_id' => Yii::$app->tm->platformId,
            'text' => $text,
            'reply_markup' => json_encode(["keyboard" => $button, 'one_time_keyboard' => true, 'resize_keyboard' => true]),
        ]);
    }

    public function saveCommand($command)
    {
        Yii::$app->tm->customer->command = $command;
        Yii::$app->tm->customer->save();
    }

    public function saveCommandNull()
    {
        Yii::$app->tm->customer->command = null;
        Yii::$app->tm->customer->save();
    }

    public function send($params = [])
    {
        $this->request = (new Request(Yii::$app->tm->bot->token, $params))->send();
        BotLogger::save_input($this->request, __METHOD__);
        return $this->request;
    }

    public function unknown()
    {
        $this->sendMessage('Вы ввели неизвестную команду');
    }

    public function deleteMessageByMessageId($messageId)
    {
        return $this->send([
            'method' => 'deleteMessage',
            'chat_id' => Yii::$app->tm->platformId,
            'message_id' => $messageId,
        ]);
    }

    public function deleteMessage()
    {
        return $this->send([
            'method' => 'deleteMessage',
            'chat_id' => Yii::$app->tm->platformId,
            'message_id' => Yii::$app->tm->messageId,
        ]);
    }

    public function deleteMessageByPlatformIdAndMessageId($platformId, $messageId)
    {
        return $this->send([
            'method' => 'deleteMessage',
            'chat_id' => $platformId,
            'message_id' => $messageId
        ]);
    }

    public function errors($errors)
    {
        if ($errors) {
            foreach ($errors as $item) {
                foreach ($item as $i) {
                    $this->sendMessage($i);
                }
            }
        }
    }

    public function text($name, $data = [])
    {
        return BotCommand::textTm($name, $data);
    }

    public function encode($data)
    {
        return Json::encode($data);
    }

    public function sendChatAction($action = 'typing')
    {
        return $this->send([
            'method' => 'sendChatAction',
            'chat_id' => Yii::$app->tm->platformId,
            'action' => $action
        ]);
    }

    public static function getUserProfilePhotos(Customer $customer)
    {
        $api = new \api\base\API(Yii::$app->tm->bot->token);
        $photo = $api->getUserProfilePhotos()->setUserId(Yii::$app->tm->platformId)->send();
        if (isset($photo->photos[0][0])) {
            $file = $api->getFile()->setFileId($photo->photos[0][0]->file_id)->send();
            $pathDirectory = Yii::getAlias('@backend/web/uploads/customer/' . $customer->id . DIRECTORY_SEPARATOR);
            BaseFileHelper::createDirectory($pathDirectory);
            $file_path = explode('.', $file->file_path);
            $extension = '.' . $file_path[1];
            if ($file->file_path) {
                $url = 'https://api.telegram.org/file/bot' . Yii::$app->tm->bot->token . DIRECTORY_SEPARATOR . $file->file_path;
                if (file_put_contents($pathDirectory . 'avatar' . $extension, file_get_contents($url))) {
                    $customer->img = $extension;
                    $customer->save();
                    return true;
                }
            }
        }
        return false;
    }

    public function sendDocument($file_path)
    {
        return $this->send([
            'method' => 'sendDocument',
            'chat_id' => Yii::$app->tm->platformId,
            'document' => $file_path,
        ]);
    }

    public function keyboardDelete($text)
    {
        return $this->send([
            'method' => 'sendMessage',
            'chat_id' => Yii::$app->tm->platformId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                "remove_keyboard" => true,
            ], true)
        ]);
    }

    public function keyboardDeleteByPlatformId($text, $platformId)
    {
        return $this->send([
            'method' => 'sendMessage',
            'chat_id' => $platformId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                "remove_keyboard" => true,
            ], true)
        ]);
    }

    public function getWebHookInfo()
    {
        $api = new API(Yii::$app->tm->bot->token);
        return $api->getWebhookInfo()->send();
    }
}
