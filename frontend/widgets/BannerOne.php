<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;

class BannerOne extends Widget
{
    public function run()
    {
        return $this->render('banner_one');
    }
}
