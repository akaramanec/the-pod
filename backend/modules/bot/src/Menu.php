<?php

namespace backend\modules\bot\src;

use backend\modules\bot\models\BotMenu;

class Menu
{

    public function __construct()
    {
        foreach (BotMenu::find()->all() as $item) {
            $this->__set($item->slug, $item->name);
        }
    }

    public function __set($attr, $value)
    {
        $this->{$attr} = $value;
    }
}
