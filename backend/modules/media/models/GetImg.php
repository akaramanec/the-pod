<?php


namespace backend\modules\media\models;

use backend\modules\bot\models\Bot;
use src\helpers\appUrl;
use Yii;
use yii\helpers\Html;

class GetImg
{
    public static function customer($item, $width = '100%')
    {
        return Html::img(self::pathCustomer($item), ['width' => $width]);
    }

    public static function pathCustomer($item)
    {
        $path = Yii::getAlias('@backend/web/uploads/customer/' . $item->id . '/avatar.jpg');
        if (is_file($path)) {
            return Yii::$app->params['dataUrl'] . '/uploads/customer/' . $item->id . '/avatar.jpg';
        }
        return Yii::$app->params['imgUrl'] . Img::pathNoImg();
    }

    public static function bot($item, $width = '100%')
    {
        return Html::img(self::pathBot($item->id), ['width' => $width]);
    }

    public static function pathBot($id)
    {
        $path = Yii::getAlias('@backend/web/uploads/bot/' . $id . '/avatar.png');
        if (is_file($path)) {
            return '/uploads/bot/' . $id . '/avatar.png';
        }
        return '/uploads/no-bot.png';
    }

    public static function iconBot($platform, $size, $width = 31)
    {
        switch ($platform) {
            case Bot::TELEGRAM:
                $icon = Html::img('/img/' . $size . '_telegram.png', ['width' => $width]);
                break;
            case Bot::VIBER:
                $icon = Html::img('/img/' . $size . '_viber.png', ['width' => $width]);
                break;
            case Bot::MESSENGER:
                $icon = Html::img('/img/' . $size . '_messenger.png', ['width' => $width]);
                break;
            default:
                $icon = Html::img('/img/' . $size . '_placeholder_bot.png', ['width' => $width]);
        }
        return $icon;
    }

    public static function bannerBot($platform)
    {
        switch ($platform) {
            case Bot::TELEGRAM:
                $icon = Html::tag('div', null, ['style' => 'background-image: url(/img/telegram.jpg)', 'class' => 'banner-bot']);
                break;
            case Bot::VIBER:
                $icon = Html::tag('div', null, ['style' => 'background-image: url(/img/viber.jpg)', 'class' => 'banner-bot']);
                break;
            case Bot::MESSENGER:
                $icon = Html::tag('div', null, ['style' => 'background-image: url(/img/messenger.png)', 'class' => 'banner-bot']);
                break;
        }
        return $icon;
    }
}
