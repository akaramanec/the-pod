<?php

namespace src\helpers;

use Yii;
use yii\bootstrap4\Alert;
use yii\web\Cookie;

class Common
{

    public static function jsonEncodeAll($i)
    {
        return json_encode($i, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }

    public static function cookieAdd($name, $value)
    {
        Yii::$app->response->cookies->add(new Cookie([
            'name' => $name,
            'value' => $value,
            'expire' => time() + 2592000,
        ]));
    }

    public static function onlyInt($i)
    {
        return preg_replace("/[^0-9]/", '', $i);
    }

    public static function percentFormatter($n)
    {
        $num = 0;
        $e = explode('.', $n);
        if (isset($e[0])) {
            $num = $e[0];
        }
        if (isset($e[1]) && $e[1]) {
            $i = (int)(substr($e[1], 0, 2));
            if ($i) {
                $num = $num . '.' . $i;
            }
        }
        $num = (float)$num;
        if ($num) {
            return $num;
        }
        return 0;
    }

    public static function isIP()
    {
        return '193.194.107.108' == $_SERVER["REMOTE_ADDR"];
    }

    public static function inventoryNumber($id)
    {
        return sprintf("%06d", $id);
    }

    public static function active_search_category($id)
    {
        if ($id == Yii::$app->request->get('id')) {
            return 'category-active';
        }
    }

    public static function activeAttribute($id)
    {
        if ($id == Yii::$app->request->get('id')) {
            return 'bg-attribute-active';
        }
    }

    public static function fullUrl()
    {
        return Yii::$app->params['homeUrl'] . $_SERVER['REQUEST_URI'];
    }

    public static function canonicalUrl()
    {
        if (isset(Yii::$app->view->params['canonical']) && Yii::$app->view->params['canonical']) {
            return Yii::$app->view->params['canonical'];
        }
        return self::fullUrl();
    }

    public static function video($video)
    {
        if (!$video) {
            return false;
        }
        $v1 = explode('=', $video);
        if (is_array($v1)) {
            $v2 = explode('&', $v1[1]);
            return $v2[0];
        } else {
            return false;
        }
    }

    public static function clearText($str)
    {
        return preg_replace('/[^a-zA-Zа-яА-Я0-9., ]/ui', '', $str);
    }

    public static function active_attribute_value($attribute_value_id)
    {
        if ($attribute_value_id == Yii::$app->request->get('attribute_value_id')) {
            return 'bg_attribute_active';
        }
    }

    public static function active_module($module_id)
    {
        if ($module_id == Yii::$app->request->get('module_id')) {
            return 'bg_active';
        }
    }

    public static function capitalLetters($str)
    {
        $s = mb_strtolower($str, "UTF-8");
        return mb_convert_case($s, MB_CASE_TITLE, "UTF-8");
    }

    public static function price($price)
    {
        return number_format($price, 0, " ", " ") . ' гр.';
    }

    public static function str($text, $start = 0, $end = 50)
    {
        if (iconv_strlen($text, 'UTF-8') > $end) {
            return mb_substr($text, $start, $end) . '..';
        } else {
            return $text;
        }
    }

    public static function activeSide($controller)
    {
        if (Yii::$app->controller->id == $controller) {
            return 'active-side';
        }
    }

    public static function activeSideParent($controllers = [])
    {
        foreach ($controllers as $controller) {
            if (Yii::$app->controller->id == $controller) {
                return 'active-side-patent';
            }
        }

    }

    public static function alert4()
    {
        $alertTypes = [
            'error' => 'alert-danger',
            'danger' => 'alert-danger',
            'success' => 'alert-success',
            'info' => 'alert-info',
            'warning' => 'alert-warning'
        ];
        foreach ($alertTypes as $k => $v) {
            if (Yii::$app->session->getFlash($k)) {
                return Alert::widget([
                    'options' => [
                        'class' => $v,
                    ],
                    'body' => Yii::$app->session->getFlash($k),
                ]);
            }
        }
    }

    public static function pager4($maxButtonCount = 20)
    {
        return [
            'pageCssClass' => ['class' => 'page-item'],
            'linkOptions' => ['class' => 'page-link'],
            'firstPageLabel' => '««',
            'lastPageLabel' => '»»',
            'firstPageCssClass' => 'page-item',
            'lastPageCssClass' => 'page-item',
            'prevPageCssClass' => 'page-item',
            'nextPageCssClass' => 'page-item',
            'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
            'maxButtonCount' => $maxButtonCount,
        ];
    }
}
