<?php

namespace backend\widgets;

use backend\modules\rent\models\Category;
use src\helpers\Common;
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
        $this->data = Category::find()->indexBy('id')->asArray()->orderBy('sort asc')->all();
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

    private function getMenuHtml($tree, $tab = '')
    {
        $str = '';
        foreach ($tree as $category) {
            $str .= $this->{$this->show}($category, $tab);
        }
        return $str;
    }

    private function select($category, $tab)
    {
        $html = Html::tag('option', $tab . $category['name'], [
            'value' => $category['id'],
            'selected' => $category['id'] == $this->model->parent_id ? true : false,
            'disabled' => $category['id'] == $this->model->id ? true : false,
        ]);
        if (isset($category['children'])) {
            $html .= $this->getMenuHtml($category['children'], $tab . '- ');
        }
        return $html;
    }

    private function li($category, $tab)
    {
        $html = '<li>';
        $html .= Html::a($category['name'], ['/rent/category/index', 'id' => $category['id']], [
            'class' => Common::active_search_category($category['id'])
        ]);
        if (isset($category['children'])) {
            $html .= '<ul>' . $this->getMenuHtml($category['children']) . '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

}
