<?php

namespace src\helpers;

use backend\modules\rent\models\Category;
use Yii;

class CategoryList
{
    public $show;
    public $model;
    public $data;
    public $tree;
    public $items;

    public function __construct($show = 'liForm')
    {
        $this->show = $show;
        $this->data = Category::find()->indexBy('id')->asArray()->orderBy('sort asc')->all();
        $this->tree = $this->getTree();
        $this->items = $this->getArray($this->tree);
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

    private function getArray($tree, $tab = '')
    {
        $a = [];
        foreach ($tree as $category) {
            $a = $a + $this->{$this->show}($category, $tab);
        }
        return $a;
    }

    private function liForm($category, $tab)
    {
        $item = [$category['id'] => $tab . $category['name']];
        if (isset($category['children'])) {
            $item = $item + $this->getArray($category['children'], $tab . ' -');
        }
        return $item;
    }
}
