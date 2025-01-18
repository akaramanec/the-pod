<?php

namespace common\widgets;

use Yii;
use yii\base\Widget;

class PaginationWidget extends Widget
{
    public $pagination_name = 'pagination';
    public $page = [20, 30, 40, 50, 100, 200];

    public function run()
    {
        $option = '';
        foreach ($this->page as $i) {
            $option .= '<option ' . $this->active($i) . ' value="' . $i . '" >' . $i . '</option>';
        }
        return $this->render('pagination', [
            'pagination_name' => $this->pagination_name,
            'option' => $option,
        ]);
    }

    private function active($value)
    {
        if (Yii::$app->request->cookies->getValue($this->pagination_name) == $value) {
            return 'selected';
        }
    }
}
