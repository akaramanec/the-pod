<?php

namespace frontend\widgets;

use yii\base\Widget;
use Yii;
use yii\helpers\Html;

class CategoryWidget extends Widget
{
    public $show;
    public $model;
    public $data;
    public $tree;

    public function run()
    {
        $this->data = Yii::$app->common->category;
        $this->tree = $this->getTree();
        return $this->getMenuHtml($this->tree);
    }

    private function getTree()
    {
        $tree = [];
        foreach ($this->data as $id => &$node) {
            if (!$node['parent_id']) {
                $tree[$id] = &$node;
            } else {
                $this->data[$node['parent_id']]['children'][$node['id']] = &$node;
            }
        }
        return $tree;
    }

    private function getMenuHtml($tree)
    {
        $str = '';
        foreach ($tree as $category) {
            $str .= $this->{$this->show}($category);
        }
        return $str;
    }

    private function li($category)
    {
        $html = '<li>';
        $html .= Html::a($category['name'], ['/catalog/' . $category['slug']], [
            'class' => isset($category['children']) ? 'dropdown-item dropdown-toggle' : 'dropdown-item'
        ]);
        if (isset($category['children'])) {
            $html .= '<ul class="dropdown-menu">' . $this->getMenuHtml($category['children']) . '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

    private function footer($category)
    {
        $html = '<li>';
        $html .= Html::a($category['name'], ['/catalog/' . $category['slug']], [
            'class' => 'footer__list-item'
        ]);
        $html .= '</li>';
        return $html;
    }
}
