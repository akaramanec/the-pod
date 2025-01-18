<?php

namespace backend\modules\bot\telegram;

use backend\modules\media\models\Img;
use backend\modules\shop\models\Faq;
use Yii;
use yii\helpers\Html;

class TFaq extends TCommon
{
    /* Только для кнопки меню. Вместо  метода listFaq */
    public function clickKeyboardFaq()
    {
        $this->session->del('selectedProduct');
        $button = $this->keyboardFaq();
        $this->delCommon();
        $this->button($this->text('startFaq'), $button);
        $this->saveSessionMessageId('mainMessageId');
        return true;
    }
    public function listFaq()
    {
        $messageId = $this->session->messageId('mainMessageId');
        $button = $this->keyboardFaq();
        $this->delCommon();
        if ($messageId) {
            $this->edit($this->text('startFaq'), $button, $messageId);
        } else {
            $this->button($this->text('startFaq'), $button);
            return $this->saveSessionMessageId('mainMessageId');
        }
    }

    public function showFaq()
    {
        if (isset(Yii::$app->tm->data->faq_id)) {
            $faq = Faq::findOne(Yii::$app->tm->data->faq_id);
            if ($faq->img) {
                $this->text .= '<a href="' . Img::mainPath(POD_FAQ, $faq->id, $faq->img, '400x400') . '">.</a>';
            }
            $this->text .= Html::tag('b', $faq->name) . PHP_EOL;
            $this->text .= $faq->text;
            $button[] = [
                ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TFaq_listFaq'])],
            ];
            $this->edit($this->text, $button, $this->session->messageId('mainMessageId'));
        }
    }

    private function keyboardFaq()
    {
        foreach (Faq::find()->andWhere(['status' => Faq::STATUS_ACTIVE])->all() as $faq) {
            $button[] = [['text' => $faq->name, 'callback_data' => json_encode([
                'action' => '/TFaq_showFaq',
                'faq_id' => $faq->id
            ])]];
        }
        return $button;
    }


}
