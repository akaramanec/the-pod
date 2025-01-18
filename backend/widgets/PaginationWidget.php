<?php

namespace backend\widgets;

use yii\base\Widget;

class PaginationWidget extends Widget
{
    public $pagination_name = 'pagination_name';

    public function run()
    {
        return $this->render('pagination', [
            'pagination_name' => $this->pagination_name,
        ]);
    }
}