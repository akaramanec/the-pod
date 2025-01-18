<?php

namespace src\cookie;

use Yii;

use yii\bootstrap4\Html;
use\yii\web\Cookie;

class Flash
{
    public static function success($message)
    {
        self::set('flash_success', $message);
    }

    public static function error($message)
    {
        self::set('flash_error', $message);
    }

    public static function set($name, $message)
    {
        Yii::$app->response->cookies->add(new Cookie([
            'name' => $name,
            'value' => $message,
            'expire' => time() + 15,
        ]));
    }

    public static function alert()
    {
        $flash_success = Yii::$app->request->cookies->getValue('flash_success');
        if ($flash_success) {
            return "<div class='alert alert-success'>" . $flash_success . self::close('flash_success') . " </div>";
        }
        $flash_error = Yii::$app->request->cookies->getValue('flash_error');
        if ($flash_error) {
            return "<div class='alert alert-danger'>" . $flash_error . self::close('flash_error') . " </div>";
        }
    }

    private static function close($name)
    {
        return Html::a('<span>&times;</span>',
            ['/base/delete-flash'], [
                'role' => 'button',
                'data-method' => 'POST',
                'data-params' => ['delete_flash' => $name],
                'class' => 'close'
            ]);
    }

    public static function remove($name)
    {
        unset(Yii::$app->response->cookies[$name]);
    }
}
