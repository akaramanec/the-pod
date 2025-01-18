<?php

namespace backend\modules\customer\controllers;

use backend\modules\customer\models\Customer;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;

class AjaxController extends Controller
{
    public $layout = false;

    public function actionRelations($relations)
    {
        $key = Json::decode($relations);
        $key_group = [];
        foreach (Customer::findAll($key) as $item) {
            if ($item->group) {
                foreach ($item->group as $i) {
                    $key_group[] = $i->id;
                    $i->group_id = null;
                    $i->save(false);
                }
            }
        }

        $group_id = array_shift($key);
        $key_common = ArrayHelper::merge($key, $key_group);
        if ($key_common) {
            foreach ($key_common as $id) {
                $customer = Customer::findOne($id);
                $customer->group_id = $group_id;
                $customer->save(false);
            }
        }
        $main_customer = Customer::findOne($group_id);
        $main_customer->group_id = null;
        $main_customer->save(false);
        return true;
    }

    public function actionDelRelations($id)
    {
        $customer = Customer::findOne($id);
        if ($customer->group_id === null) {
            $children = Customer::findOne($customer->group[0]->id);
            if (count($customer->group) == 1) {
                $children->group_id = null;
                $children->save(false);
                return true;
            } elseif (count($customer->group) > 1) {
                foreach ($customer->group as $item) {
                    $item->group_id = $children->id;
                    $item->save(false);
                }
                $children->group_id = null;
                $children->save(false);
                return true;
            }
        }
        $customer->group_id = null;
        $customer->save(false);
        return true;
    }
}
