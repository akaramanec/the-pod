<?php

namespace src\helpers;

use Yii;
use yii\bootstrap4\Html;

class Buttons
{
    public static function create($title = '', $view = 'create')
    {
        if (is_string($view)) {
            $view = [$view];
        }
        return Html::a('<i class="fas fa-plus"></i>' . ' ' . $title,
            $view,
            ['class' => 'btn btn-outline-success btn-dashboard', 'title' => 'Добавить', 'role' => 'button']);
    }

    public static function clearAllFilters($url = ['index'], $title = 'Очистить фильтры')
    {
        return Html::a('<i class="fas fa-broom"></i>' . ' ' . $title,
            $url,
            ['class' => 'btn btn-outline-warning btn-dashboard', 'title' => 'Очистить фильтры', 'role' => 'button']);
    }

    public static function reset_filters($url = [])
    {
        return Html::a('<i class="fas fa-broom"></i>', $url, [
            'class' => 'btn btn-danger',
            'role' => 'button',
            'title' => 'Сбросить фильтры',
        ]);
    }

    public static function eventStatusManual()
    {
        return Html::button('<i class="fas fa-book-open"></i>',
            [
                'class' => 'btn btn-outline-info event_status_manual',
                'title' => 'Статус',
                'data-toggle' => 'modal',
                'data-target' => '#event_status_manual',
            ]);
    }

    public static function artistStatusManual()
    {
        return Html::button('<i class="fas fa-book-open"></i>',
            [
                'class' => 'btn btn-outline-info artist_status_manual',
                'title' => 'Статус',
                'data-toggle' => 'modal',
                'data-target' => '#artist_status_manual',
            ]);
    }

    public static function loggerDeleteAll()
    {
        return Html::a('<i class="fas fa-broom"></i>', ['/bot/logger/delete-all'], [
            'class' => 'btn btn-danger',
            'role' => 'button',
            'title' => 'Очистить',
            'data-confirm' => 'Вы уверены, что хотите очистить логер?'
        ]);
    }
}
