<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;

class Meta extends Widget
{

    public $title;
    public $description;
    public $url;
    public $img;

    public function run()
    {
        $this->url = \src\helpers\Common::fullUrl();
        return $this->render('meta', [
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'img' => $this->img,
        ]);
    }
}
