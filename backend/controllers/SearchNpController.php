<?php

namespace backend\controllers;

use src\services\np\Search;
use Yii;
use yii\rest\Controller;

/**
 * @property Search $_search
 */
class SearchNpController extends Controller
{
    private $_search;

    public function init()
    {
        parent::init();
        $session = Yii::$app->session;
        $session->open();
        $this->_search = new Search();
    }

    public function actionSearchCity($q)
    {
        return $this->renderAjax('@frontend/views/search-np/_city', [
            'city' => $this->_search->city($q)
        ]);
    }

    public function actionWarehouses()
    {
        return $this->renderAjax('@frontend/views/search-np/_branch', [
            'listWarehouses' => $this->_search->warehouses(Yii::$app->request->post('item_city')),
            'value' => ''
        ]);
    }

    public function actionWarehousesData()
    {
        Yii::$app->response->format = 'json';
        return $this->_search->warehouses(Yii::$app->request->post('item_city'));
    }

    public function actionBranchSave()
    {
        $this->_search->branchSave(Yii::$app->request->post('branch_ref'));
    }
}

