<?php

namespace backend\modules\bot\viber;

use backend\modules\media\models\Img;
use backend\modules\shop\models\Faq;
use src\helpers\Common;
use Yii;

class VFaq extends VCommon
{

    public function listFaq()
    {
        $this->keyboard($this->text('startFaq'), $this->keyboardFaq());
    }

    public function showFaq()
    {
        if (isset(Yii::$app->vb->data->faq_id)) {
            $faq = Faq::findOne(Yii::$app->vb->data->faq_id);
            if ($faq->img) {
                $img = Yii::$app->params['dataUrl'] . Img::cache(POD_FAQ, $faq->id, $faq->img, '400x400');
                $this->sendPhoto($faq->name, $img);
                $this->keyboard($faq->text, $this->keyboardFaq());
            } else {
                $this->text .= $faq->name . PHP_EOL;
                $this->text .= $faq->text;
                $this->keyboard($this->text, $this->keyboardFaq());
            }
        }
    }

    private function keyboardFaq()
    {
        $this->keyboard[] = $this->buttonMainMenu();
        foreach (Faq::find()->andWhere(['status' => Faq::STATUS_ACTIVE])->all() as $faq) {
            $text = '<font color="#ffffff"><b>' . Common::str($faq->name, 0, 95) . '</b></font>';
            $this->keyboard[] = $this->buttonImgText($text, '/img/vb/empty-min.jpg', ['action' => 'VFaq_showFaq', 'faq_id' => $faq->id]);
        }
        $this->keyboard[] = $this->buttonMainMenu();
        return $this->keyboard;
    }


}
