<?php

namespace src\services\np;

use backend\modules\bot\models\BotLogger;
use backend\modules\bot\src\AddressNp;
use backend\modules\bot\src\ApiNp;
use src\helpers\Common;
use Yii;
use yii\helpers\Json;

/**
 * @property AddressNp $addressNp
 */
class Search
{
    public $addressNp;
    public $session;

    public function __construct()
    {
        $session = Yii::$app->session;
        $session->open();
        $this->addressNp = new AddressNp(new ApiNp());
    }

    public function city($q)
    {
        if (isset($_SESSION['item_branch'])) {
            unset($_SESSION['item_branch']);
        }
        $response = $this->addressNp->searchCity($q);
        $city = [];
        if ($response->success == true && isset($response->data[0]->Addresses) && $response->data[0]->Addresses) {
            foreach ($response->data[0]->Addresses as $item) {
                $city[] = [
                    'name' => $item->Present,
                    'item_city' => Common::jsonEncodeAll($item)
                ];
            }
        } else {
            $city[] = [
                'name' => 'Ничего не найдено',
                'item_city' => null
            ];
        }
        return $city;
    }

    public function warehouses($item_city)
    {
        $item_city = Json::decode($item_city);
        $listWarehouses[] = ['id' => 0, 'text' => 'Выбрать отделение'];
        if (!isset($item_city['Ref'])) {
            return $listWarehouses;
        }
        $_SESSION['item_city'] = $item_city;
        $response = $this->addressNp->warehouses($item_city['Ref']);
//        BotLogger::save_input($response, '$response');

        if ($response->success == true) {
            foreach ($response->data as $data) {
                if (isset($data->CategoryOfWarehouse) && ($data->CategoryOfWarehouse == 'Branch' || $data->CategoryOfWarehouse == 'Store') && isset($data->Ref) && isset($data->DescriptionRu)) {
                    $listWarehouses[] = ['id' => $data->Ref, 'text' => $data->DescriptionRu];
                }
            }
        }
        return $listWarehouses;
    }

    public function branchSave($branch_ref)
    {
        if (isset($_SESSION['item_city']['Ref'])) {
            $addressNp = new AddressNp(new ApiNp());
            $response = $addressNp->warehouses($_SESSION['item_city']['Ref']);
            foreach ($response->data as $data) {
                if ($data->Ref == $branch_ref) {
                    $_SESSION['item_branch'] = json_decode(Common::jsonEncodeAll($data), true);
                }
            }
        }
    }

    public static function delSessionNp()
    {
        if (isset($_SESSION['item_city'])) {
            unset($_SESSION['item_city']);
        }
        if (isset($_SESSION['item_branch'])) {
            unset($_SESSION['item_branch']);
        }
    }
}
