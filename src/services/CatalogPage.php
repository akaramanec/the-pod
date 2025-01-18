<?php

namespace src\services;

use backend\modules\media\models\Img;
use src\helpers\CategoryHelp;
use Yii;

class CatalogPage
{
    public $meta;
    public $name;
    public $breadcrumbs;
    public $slug;
    public $children;
    public $description;
    public $img;
    public $url;
    private $_model;

    public function __construct($model, $mode)
    {
        $this->_model = $model;
        if ($mode == 'slug') {
            $this->slug();
        }
        if ($mode == 'catalog-all') {
            $this->catalogAll();
        }
    }

    public function slug()
    {
        $this->name = $this->_model->name;
        $this->meta = $this->_model->meta;
        $this->slug = $this->_model->slug;
        $this->url = '/catalog/' . $this->_model->slug;
        $this->children = $this->_model->childrenActive;
        $this->breadcrumbs = CategoryHelp::parents($this->_model, 'breadcrumbs');
        $this->microMarkingBreadcrumb = CategoryHelp::parents($this->_model, 'microMarking');
        $this->description = $this->_model->description;
        $this->img =  Img::mainPath(SHOP_CATEGORY, $this->_model->id, $this->_model->img, '260x168');
    }

    public function catalogAll()
    {
        $this->name = $this->_model->lang->name;
        $this->meta = $this->_model->lang->meta;
        $this->slug = 'catalog';
        $this->url = '/catalog';
        $this->children = [];
        $this->breadcrumbs = ['label' => $this->_model->lang->name];
        $this->microMarkingBreadcrumb = [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Все Товары',
            'item' => Yii::$app->params['homeUrl'] . '/catalog'
        ];
        $this->description = $this->_model->lang->content;
        $this->img = Img::pathNoImg();
    }

}
