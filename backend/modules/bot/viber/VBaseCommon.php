<?php

namespace backend\modules\bot\viber;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotCommand;
use backend\modules\bot\models\BotLogger;
use Viber\Api\Keyboard\Button;
use Viber\Api\Message\File;
use Viber\Api\Message\Picture;
use Viber\Api\Message\Text;
use Viber\Api\Sender;
use Viber\Client;
use Yii;
use yii\helpers\Json;

/**
 * https://partners.viber.com/
 * viber://pa/info?uri=poddev
 * viber://pa?chatURI=poddev
 * ->setColumns(6) max
 * ->setRows(7)    max
 */
class VBaseCommon
{
    public $botVider;
    public $botSender;
    private $_token;
    public $mainBg = '#151515';
    public $buttonBg = '#151515';

    public function __construct()
    {
        $bot = Bot::bot(Bot::VIBER);
        $this->_token = $bot->token;
        $this->botVider = new \Viber\Bot(['token' => $this->_token]);
        $this->botSender = new Sender([
            'name' => Yii::$app->name,
            'avatar' => Yii::$app->params['dataUrl'] . '/img/logo-50x50.jpg'
        ]);
    }

    public function sendMessage($text)
    {
        $this->botVider->getClient()->sendMessage(
            (new Text())
                ->setSender($this->botSender)
                ->setReceiver(Yii::$app->vb->platformId)
                ->setText($text)
        )->getData();
    }

    public function sendMessageByChatId($chat_id, $text)
    {
        $this->botVider->getClient()->sendMessage(
            (new Text())
                ->setSender($this->botSender)
                ->setReceiver($chat_id)
                ->setText($text)
        )->getData();
    }

    public function sendPhoto($text, $img)
    {
        $this->botVider->getClient()->sendMessage(
            (new Picture())
                ->setSender($this->botSender)
                ->setReceiver(Yii::$app->vb->platformId)
                ->setMedia($img)
                ->setText($text)
        );
    }

    public function sendFileByChatId($chat_id, $text)
    {
        $this->botVider->getClient()->sendMessage(
            (new File())
                ->setSender($this->botSender)
                ->setReceiver($chat_id)
                ->setText($text)
        )->getData();
    }

    public function keyboard($text, $keyboard, $fieldState = 'hidden')
    {
        $this->botVider->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
                ->setSender($this->botSender)
                ->setMinApiVersion(7)
                ->setReceiver(Yii::$app->vb->platformId)
                ->setText($text)
                ->setKeyboard((new \Viber\Api\Keyboard())
                    ->setBgColor($this->mainBg)
                    ->setInputFieldState($fieldState)
                    ->setButtons($keyboard))
        )->getData();
    }

    public function sendPictureKeyboard($text, $keyboard, $img)
    {
        $this->botVider->getClient()->sendMessage(
            (new Picture())
                ->setSender($this->botSender)
                ->setMinApiVersion(7)
                ->setReceiver(Yii::$app->vb->platformId)
                ->setMedia(Yii::$app->params['imgUrl'] . $img)
                ->setThumbnail(Yii::$app->params['imgUrl'] . $img)
                ->setText($text)
                ->setKeyboard((new \Viber\Api\Keyboard())
                    ->setBgColor($this->mainBg)
                    ->setInputFieldState('hidden')
                    ->setButtons($keyboard))
        )->getData();
    }

    public function button($text, $actionBody, $columns = 6, $rows = 1)
    {
        return (new Button())
            ->setRows($rows)
            ->setColumns($columns)
            ->setActionType('reply')
            ->setBgColor($this->mainBg)
            ->setSilent(true)
            ->setTextSize('small')
            ->setActionBody($this->encode($actionBody))
            ->setText('<font color="#ffffff">' . $text . '</font>');
    }

    public function buttonNone($text, $img, $columns = 6, $rows = 1)
    {
        return (new Button())
            ->setRows($rows)
            ->setColumns($columns)
            ->setActionType('reply')
            ->setBgColor($this->mainBg)
            ->setSilent(true)
            ->setTextOpacity(0)
            ->setActionBody('none')
            ->setBgMedia(Yii::$app->params['imgUrl'] . $img)
            ->setText('<font color="#ffffff">' . $text . '</font>');
    }

    public function buttonNoneText($text, $img, $columns = 6, $rows = 1)
    {
        return (new Button())
            ->setRows($rows)
            ->setColumns($columns)
            ->setActionType('reply')
            ->setBgColor($this->mainBg)
            ->setSilent(true)
            ->setActionBody('none')
            ->setBgMedia(Yii::$app->params['imgUrl'] . $img)
            ->setText('<font color="#ffffff">' . $text . '</font>');
    }

    public function sendPicture($text, $img)
    {
        $this->botVider->getClient()->sendMessage(
            (new Picture())
                ->setSender($this->botSender)
                ->setMinApiVersion(7)
                ->setReceiver(Yii::$app->vb->platformId)
                ->setMedia(Yii::$app->params['imgUrl'] . $img)
                ->setThumbnail(Yii::$app->params['imgUrl'] . $img)
                ->setText($text)
        );
    }

    public function buttonImg($text, $img, $actionBody, $columns = 6, $rows = 1)
    {
        return (new Button())
            ->setRows($rows)
            ->setColumns($columns)
            ->setActionType('reply')
            ->setSilent(true)
            ->setText($text)
            ->setTextOpacity(0)
            ->setBgColor($this->mainBg)
            ->setBgMedia(Yii::$app->params['imgUrl'] . $img)
            ->setActionBody($this->encode($actionBody));
    }

    public function buttonImgText($text, $img, $actionBody, $columns = 6, $rows = 1)
    {
        return (new Button())
            ->setRows($rows)
            ->setColumns($columns)
            ->setActionType('reply')
            ->setSilent(true)
            ->setText('<font color="#ffffff">' . $text . '</font>')
            ->setBgMedia(Yii::$app->params['imgUrl'] . $img)
            ->setActionBody($this->encode($actionBody));
    }

    public function buttonUrlImg($text, $img, $actionBody, $columns = 6, $rows = 1)
    {
        return (new Button())
            ->setRows($rows)
            ->setColumns($columns)
            ->setActionType('open-url')
            ->setText('<font color="#ffffff">' . $text . '</font>')
            ->setBgColor($this->mainBg)
            ->setTextOpacity(0)
            ->setBgMedia(Yii::$app->params['imgUrl'] . $img)
            ->setSilent(true)
            ->setActionBody($actionBody);
    }

    public function buttonUrlImgText($text, $img, $actionBody, $columns = 6, $rows = 1)
    {
        return (new Button())
            ->setRows($rows)
            ->setColumns($columns)
            ->setActionType('open-url')
            ->setText('<font color="#ffffff">' . $text . '</font>')
            ->setBgColor($this->mainBg)
            ->setBgMedia(Yii::$app->params['imgUrl'] . $img)
            ->setSilent(true)
            ->setActionBody($actionBody);
    }

    public function sharePhone($text)
    {
        $this->botVider->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
                ->setSender($this->botSender)
                ->setMinApiVersion(7)
                ->setReceiver(Yii::$app->vb->platformId)
                ->setText($text)
                ->setKeyboard((new \Viber\Api\Keyboard())
                    ->setInputFieldState('hidden')
                    ->setButtons([
                        (new \Viber\Api\Keyboard\Button())
                            ->setBgColor($this->mainBg)
                            ->setBgMedia(Yii::$app->params['imgUrl'] . '/img/vb/podel-min.jpg')
                            ->setTextOpacity(0)
                            ->setActionType('share-phone')
                            ->setTextSize('large')
                            ->setActionBody('reply')
                            ->setText('Поделиться номером')
                    ])
                )
        );
    }

    public function carousel($button, $row = 7, $columns = 6)
    {
        $this->botVider->getClient()->sendMessage(
            (new \Viber\Api\Message\CarouselContent())
                ->setSender($this->botSender)
                ->setReceiver(Yii::$app->vb->platformId)
                ->setButtonsGroupColumns($columns)
                ->setButtonsGroupRows($row)
                ->setBgColor($this->mainBg)
                ->setButtons($button)
        );
    }

    public function buttonUrl($text, $actionBody, $columns = 6, $rows = 1)
    {
        return (new Button())
            ->setRows($rows)
            ->setColumns($columns)
            ->setActionType('open-url')
            ->setBgColor($this->mainBg)
            ->setSilent(true)
            ->setActionBody($actionBody)
            ->setText('<font color="#ffffff">' . $text . '</font>');
    }


    public function keyboardByCustomer($text, $keyboard, $customer)
    {
        $this->botVider->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
                ->setMinApiVersion(7)
                ->setSender($this->botSender)
                ->setReceiver($customer->platform_id)
                ->setText($text)
                ->setKeyboard((new \Viber\Api\Keyboard())
                    ->setBgColor($this->mainBg)
                    ->setButtons($keyboard))
        );
    }

    public function carouselAndKeyboard($button, $keyboard, $row = 7, $columns = 6)
    {
        $this->botVider->getClient()->sendMessage(
            (new \Viber\Api\Message\CarouselContent())
                ->setSender($this->botSender)
                ->setMinApiVersion(7)
                ->setReceiver(Yii::$app->vb->platformId)
                ->setButtonsGroupColumns($columns)
                ->setButtonsGroupRows($row)
                ->setBgColor($this->mainBg)
                ->setButtons($button)
                ->setKeyboard((new \Viber\Api\Keyboard())
                    ->setInputFieldState('hidden')
                    ->setBgColor($this->mainBg)
                    ->setButtons($keyboard))
        );
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

    public function saveCommand($command)
    {
        Yii::$app->vb->customer->command = $command;
        Yii::$app->vb->customer->save();
    }

    public function saveCommandNull()
    {
        Yii::$app->vb->customer->command = null;
        Yii::$app->vb->customer->save();
    }

    public function text($name, $data = [])
    {
        return BotCommand::textVb($name, $data);
    }

    public function encode($data)
    {
        return Json::encode($data);
    }

    public function setWebHook()
    {
        $url = Yii::$app->urlManager->createAbsoluteUrl(['/bot/hook/viber']);
        $req = new Client(['token' => $this->_token]);
        $req->getAccountInfo();
        return $req->setWebhook($url)->getData();
    }

    public function getAccountInfo()
    {
        $req = new Client(['token' => $this->_token]);
        return $req->getAccountInfo();
    }
}
