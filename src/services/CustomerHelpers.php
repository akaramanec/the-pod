<?php

namespace src\services;

use backend\modules\customer\models\Customer;
use backend\modules\media\models\GetImg;
use Yii;
use yii\bootstrap4\Html;


class CustomerHelpers
{
    public $models = [];
    private $_models;
    private $_item;

    public function __construct($models)
    {
        $this->_models = $models;
        $this->getModels();
    }

    private function getModels()
    {
        foreach ($this->_models as $item) {
            if ($item->group_id === null) {
                $this->_item = $item;
                $customer = new Customer();
                $customer->id = $this->_item->id;
                $customer->view_id = $this->attribute('id');
                $customer->first_name = $this->firstName();
                $customer->phone = $this->attribute('phone');
                $customer->email = $this->attribute('email');
                $customer->icons_platform = $this->iconsPlatform();
                $customer->action = $this->action();
                $this->models[] = $customer;
            };
        }
    }

    private function attribute($attribute)
    {
        if (empty($this->_item->group)) {
            return $this->_item->{$attribute};
        }
        $i = '';
        $i .= $this->_item->{$attribute} . $this->border();
        foreach ($this->_item->group as $child) {
            $i .= $child->{$attribute} . $this->border();;
        }
        return $this->substr($i);
    }

    private function firstName()
    {
        if (empty($this->_item->group)) {
            return GetImg::customer($this->_item, 25) . ' ' . $this->firstNameLink($this->_item);
        }
        $i = '';
        $i = GetImg::customer($this->_item, 25) . ' ' . $this->firstNameLink($this->_item) . $this->border();
        foreach ($this->_item->group as $child) {
            $i .= GetImg::customer($child, 25) . ' ' . $this->firstNameLink($child) . $this->border();;
        }
        return $this->substr($i);
    }

    private function firstNameLink($item)
    {
        return Html::a($item->first_name . ' ' . $item->last_name,
            ['/customer/customer/update', 'id' => $item->id],
            ['title' => Yii::t('app', 'Edit')]);
    }

    private function iconsPlatform()
    {
        if (empty($this->_item->group)) {
            return self::iconsBot($this->_item);
        }
        $i = '';
        $i .= self::iconsBot($this->_item) . $this->border();
        foreach ($this->_item->group as $child) {
            $i .= self::iconsBot($child) . $this->border();
        }
        return $this->substr($i);
    }

    private function iconsBot()
    {
        $ul = '<ul class="icons_bot">';
        $ul .= '<li>' . GetImg::iconBot($this->_item->bot->platform, '31x31') . '</li>';
        $ul .= '<li>' . GetImg::bot($this->_item, '31x31') . '</li>';
        $ul .= '</ul>';
        return $ul;
    }

    private function action()
    {
        if (empty($this->_item->group)) {
            return $this->editLink();
        }
        $i = '';
        $i .= $this->delRelations($this->_item);
        foreach ($this->_item->group as $child) {
            $i .= $this->delRelations($child) . $this->border();
        }
        return $this->substr($i);
    }

    private function delRelations($item)
    {
        return Html::button('<i class="far fa-object-ungroup"></i>',
            [
                'class' => 'del-relations btn btn-info',
                'data-id' => $item->id,
                'title' => 'Отделить от группы ' . $item->first_name . ' ' . $item->last_name,
                'data-confirm' => 'Вы уверены, что хотите удалить из группы?',
            ]);
    }

    private function editLink()
    {
        return Html::a('<i class="far fa-edit"></i>', ['/customer/customer/update', 'id' => $this->_item->id],
            [
                'class' => 'btn btn-primary',
                'title' => 'Изменить',
            ]);
    }

    private function actionDiv()
    {
        $ul = '<ul>';
        $ul .= '<li>' . GetImg::iconBot($this->_item->bot->platform, '31x31') . '</li>';
        $ul .= '<li>' . GetImg::bot($this->_item, '31x31') . '</li>';
        $ul .= '</ul>';
        return $ul;
    }

    private function border()
    {
        return '<br>';
//        return '<hr>';
    }

    private function substr($str)
    {
        return substr($str, 0, -4);
    }
}
