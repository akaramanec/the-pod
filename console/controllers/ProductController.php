<?php

namespace console\controllers;

use backend\modules\bot\models\BotLogger;
use backend\modules\bot\src\ApiProduct;
use src\parser\CategorySave;
use src\parser\ImgSave;
use src\parser\ProductModAndAttribute;
use src\parser\ProductModTempSave;
use src\parser\ProductSave;
use src\parser\ProductTempSave;
use Yii;
use yii\console\Controller;

class ProductController extends Controller
{

    public function actionIndex()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $api = new ApiProduct();
            new CategorySave($api);
            new ProductTempSave($api);
            new ProductSave($api);
            new ProductModTempSave($api);
            new ProductModAndAttribute($api);
            new ImgSave($api);

            BotLogger::save_input(['status' => 'ok'], __METHOD__);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            BotLogger::save_input(['status' => 'error', 'message' => $e->getMessage()], __METHOD__);
        }
    }

    public function actionImgSave()
    {
        try {
            $api = new ApiProduct();
            new ImgSave($api, true);
            BotLogger::save_input(['status' => 'ok'], __METHOD__);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            BotLogger::save_input([
                'status' => 'error',
                'message' => $e->getMessage()
            ], __METHOD__);
        }
    }

}
