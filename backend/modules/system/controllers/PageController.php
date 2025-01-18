<?php

namespace backend\modules\system\controllers;

use backend\modules\system\models\search\SitePageSearch;
use backend\modules\system\models\SitePage;
use backend\modules\system\models\SitePageL;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

class PageController extends Controller
{
    public $layout = 'base';

    public function actionIndex()
    {
        $searchModel = new SitePageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $page = new SitePage();
        $page->slug = 'slug' . time();
        $page->save(false);
        foreach (LANGUAGE as $lang) {
            $name = 'name' . time();
            $pageL = new SitePageL();
            $pageL->page_id = $page->id;
            $pageL->lang = $lang;
            $pageL->name = $name;
            $pageL->description = $name;
            $pageL->content = $name;
            $pageL->meta = '{"title": "title", "description": "description"}';
            $pageL->save(false);
        }
        return $this->redirect(['update', 'id' => $page->id]);
    }

    public function actionUpdate($id)
    {
        $page = SitePage::findOne($id);
        $pageL = SitePageL::find()->where(['page_id' => $page->id])->andWhere(['lang' => Yii::$app->language])->one();
        if ($page->load(Yii::$app->request->post()) &&
            $page->save(false) &&
            $pageL->load(Yii::$app->request->post()) &&
            $pageL->save(false)) {
            return $this->redirect(['update', 'id' => $page->id]);
        }
        return $this->render('update', [
            'page' => $page,
            'pageL' => $pageL,
        ]);
    }


    public function actionSort($sort)
    {
        foreach (Json::decode($sort) as $key => $id) {
            Yii::$app->db->createCommand("UPDATE site_page SET sort=:sort WHERE id=:id")
                ->bindValue(':id', $id)
                ->bindValue(':sort', $key)
                ->execute();
        }
    }
}
