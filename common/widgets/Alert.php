<?php

namespace common\widgets;

use Yii;

/**
 * Yii::$app->session->setFlash('error', 'This is the message');
 * Yii::$app->session->setFlash('success', 'This is the message');
 * Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 */
class Alert extends \yii\bootstrap\Widget
{

    public $alertTypes = [
        'primary' => 'alert-primary',
        'secondary' => 'alert-secondary',
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        'light' => 'alert-light',
        'dark' => 'alert-dark',
    ];

    public $closeButton = [];

    public function run()
    {
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();

        foreach ($flashes as $type => $flash) {
            if (!isset($this->alertTypes[$type])) {
                continue;
            }
            foreach ((array)$flash as $i => $message) {
                return '<div class="alert ' . $this->alertTypes[$type] . ' alert-dismissible fade show" role="alert">
                        ' . $message . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>';
            }
            $session->removeFlash($type);
        }
    }
}
