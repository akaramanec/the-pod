<?php

namespace frontend\controllers;

use backend\modules\shop\models\Faq;
use backend\modules\shop\models\ProductMod;
use backend\modules\system\models\ReviewSlider;
use backend\modules\system\models\Setting;
use backend\modules\system\models\SitePage;
use backend\modules\system\models\Staff;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class HomeController extends Controller
{

    public function actionIndex()
    {
        $productMod = ProductMod::find()
            ->alias('mod')
            ->where(['mod.status' => ProductMod::STATUS_ACTIVE])
            ->andWhere(['mod.code' => Setting::code()])
            ->joinWith(['product AS product'])
            ->limit(10)
            ->all();
        $reviewSlider = ReviewSlider::find()->where(['status' => ReviewSlider::STATUS_ACTIVE])->all();
        return $this->render('index', [
            'productMod' => $productMod,
            'reviewSlider' => $reviewSlider,
            'page' => SitePage::page('home')
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about', [
            'page' => SitePage::page('about'),
            'staffs' => Staff::find()->where(['status' => Staff::STATUS_ACTIVE])->limit(3)->all()
        ]);
    }

    public function actionFaq()
    {
        return $this->render('faq', [
            'page' => SitePage::page('faq'),
            'faqs' => Faq::find()->where(['status' => Faq::STATUS_ACTIVE])->all()
        ]);
    }

    public function actionProduction()
    {
        return $this->render('production', [
            'page' => SitePage::page('production')
        ]);
    }

    public function actionReturnAndExchangeRules()
    {
        return $this->render('return-and-exchange-rules', [
            'page' => SitePage::page('return-and-exchange-rules'),
        ]);
    }

    public function actionPrivacyPolicy()
    {
        return $this->render('privacy-policy', [
            'page' => SitePage::page('privacy-policy')
        ]);
    }

    public function actionToCall($phone)
    {
        return $this->redirect('tel:' . $phone);
    }

    public function actionDemo()
    {
        return $this->render('demo');
    }

    public function actionReferralVb($ref)
    {
        return $this->redirect(Yii::$app->params['chatVb'] . '&context=ref_' . $ref);
    }

    public function actionLinkBot($name)
    {
        return $this->redirect(Yii::$app->params['chatVb'] . '&context=linkBot_' . $name);
    }

    public function actionMaintenanceMode()
    {
        $this->layout = false;
        return $this->render('maintenance-mode');
    }
}
