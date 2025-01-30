<?php

namespace src\services;

use backend\modules\admin\models\AuthItem;
use Yii;

class Role
{
    public static function check($action)
    {
        foreach (self::access($action) as $role) {
            if (Yii::$app->user->can($role)) {
                return true;
            }
        }
    }

    public static function access($action)
    {
        $data = [
            'statistics' => [AuthItem::DEV, AuthItem::ADMIN],
            'analytics' => [AuthItem::DEV, AuthItem::ADMIN],
            'bloggers' => [AuthItem::DEV, AuthItem::ADMIN],
            'customer' => [AuthItem::DEV, AuthItem::ADMIN],
            'newsletter' => [AuthItem::DEV, AuthItem::ADMIN],
            'link-bot' => [AuthItem::DEV, AuthItem::ADMIN],
            'attribute' => [AuthItem::DEV, AuthItem::ADMIN],
            'delivery' => [AuthItem::DEV, AuthItem::ADMIN],
            'content' => [AuthItem::DEV, AuthItem::ADMIN],
            'command' => [AuthItem::DEV, AuthItem::ADMIN],
            'faq' => [AuthItem::DEV, AuthItem::ADMIN],
            'notice' => [AuthItem::DEV, AuthItem::ADMIN],
            'notice-np' => [AuthItem::DEV, AuthItem::ADMIN],
            'review-slider' => [AuthItem::DEV, AuthItem::ADMIN],
            'tag' => [AuthItem::DEV, AuthItem::ADMIN],
            'page' => [AuthItem::DEV, AuthItem::ADMIN],
            'site-page-l' => [AuthItem::DEV, AuthItem::ADMIN],
            'placeholder' => [AuthItem::DEV, AuthItem::ADMIN],
            'system' => [AuthItem::DEV, AuthItem::ADMIN],
            'setting' => [AuthItem::DEV, AuthItem::ADMIN],
            'bot' => [AuthItem::DEV, AuthItem::ADMIN],
            'seo' => [AuthItem::DEV, AuthItem::ADMIN],
            'user-del' => [AuthItem::DEV, AuthItem::ADMIN],
            'admin' => [AuthItem::DEV, AuthItem::ADMIN],
            'staff' => [AuthItem::DEV, AuthItem::ADMIN],
            'set-manager' => [AuthItem::DEV, AuthItem::ADMIN],
            'all-orders-view' => [AuthItem::DEV, AuthItem::ADMIN],
            'order-delete' => [AuthItem::DEV, AuthItem::ADMIN],
            'attribute-update' => [AuthItem::DEV, AuthItem::ADMIN],
            'orders' => [AuthItem::DEV, AuthItem::ADMIN, AuthItem::MANAGER],
            'order-poll&poll' => [AuthItem::DEV, AuthItem::ADMIN, AuthItem::MANAGER],
            'order-poll' => [AuthItem::DEV, AuthItem::ADMIN, AuthItem::MANAGER],
            'poll' => [AuthItem::DEV, AuthItem::ADMIN],
            'dev' => [AuthItem::DEV],
            'auth-logger' => [AuthItem::DEV],
            'bot-menu' => [AuthItem::DEV],
            'bot-menu-command' => [AuthItem::DEV],
            'logger' => [AuthItem::DEV],
            'home' => [AuthItem::DEV],
            'notification' => [AuthItem::DEV, AuthItem::ADMIN],
            'shop' => [AuthItem::DEV, AuthItem::ADMIN],
            'category' => [AuthItem::DEV, AuthItem::ADMIN],
        ];
        return $data[$action];
    }
}
