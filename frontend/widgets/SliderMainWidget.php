<?php

namespace frontend\widgets;

use backend\modules\system\models\WidgetSliderHome;
use yii\base\Widget;

class SliderMainWidget extends Widget
{
    public function run()
    {
        return $this->render('slider-main', [
            'sliders' => WidgetSliderHome::find()->where(['status' => WidgetSliderHome::STATUS_ACTIVE])->orderBy('sort asc')->asArray()->all()
        ]);
    }
}
