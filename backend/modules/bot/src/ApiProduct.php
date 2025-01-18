<?php

namespace backend\modules\bot\src;

use backend\modules\bot\models\BotLogger;
use backend\modules\customer\models\Customer;
use backend\modules\media\models\Images;
use backend\modules\media\models\ImgSave;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderMoysklad;
use backend\modules\system\models\Setting;
use frontend\models\cart\Cart;
use frontend\models\cart\CartData;
use GuzzleHttp\Client;
use src\services\AdditionalDiscount;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;

class ApiProduct
{
    protected $_url = 'https://online.moysklad.ru/api/remap/1.2';
    protected $_login;
    protected $_pass;
    protected $_token;

    public function __construct()
    {
        $s = Setting::listValue('moysklad');
        $this->_login = $s['login'];
        $this->_pass = $s['password'];
        $this->_token = $s['token'];
    }

    protected function curl($options = [])
    {

        $curl = curl_init($options['url']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        curl_setopt($curl, CURLOPT_USERPWD, $this->_login . ':' . $this->_pass);
        if ($options['mode'] == 'post') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($options['data']));
            curl_setopt($curl, CURLOPT_POST, true);
        }
        if ($options['mode'] == 'get') {
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        }
        if ($options['mode'] == 'put') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            $data = $options['data'] == '' ? '' : json_encode($options['data']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if ($options['mode'] == 'delete') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $output = curl_exec($curl);
        curl_close($curl);
        return Json::decode($output);
    }

    public function headers()
    {
        return [
            'Accept: application/json;charset=utf-8',
            'Cache-Control: no-cache',
            'Lognex-Pretty-Print-JSON: true',
            'Content-Type: application/json'
        ];
    }

    protected function post($url, $data)
    {
        return $this->curl([
            'headers' => $this->headers(),
            'mode' => 'post',
            'url' => $this->_url . $url,
            'data' => $data
        ]);
    }

    protected function get($url)
    {
        return $this->curl([
            'headers' => $this->headers(),
            'mode' => 'get',
            'url' => $this->_url . $url,
        ]);
    }

    protected function put($url, $data = [])
    {
        return $this->curl([
            'headers' => $this->headers(),
            'mode' => 'put',
            'url' => $this->_url . $url,
            'data' => $data
        ]);
    }

    protected function delete($url)
    {
        return $this->curl([
            'headers' => $this->headers(),
            'mode' => 'delete',
            'url' => $this->_url . $url,
        ]);
    }

    public function categories()
    {
        return $this->get('/entity/productfolder');
    }

    public function category($id)
    {
        return $this->get('/entity/productfolder/' . $id);
    }

    public function productsAll($options = [])
    {
        $query = [];
        if (isset($options['limit'])) {
            $query['limit'] = $options['limit'];
        }
        if (isset($options['offset'])) {
            $query['offset'] = $options['offset'];
        }
        if ($query) {
            return $this->get('/entity/product?' . http_build_query($query));
        } else {
            return $this->get('/entity/product');
        }
    }

    public function productsByCategory($pathName)
    {
        $query = http_build_query([
            'filter' => 'pathName=' . $pathName
        ]);
        return $this->get('/entity/product?' . $query);
    }

    public function product($id)
    {
//        return $this->get('/entity/product/' . $id);
        return $this->get('/entity/product/' . $id . '?expand=images');
    }

    public function productCreate($data)
    {
        return $this->post('/entity/product', $data);
    }

    public function productEdit($id, $data)
    {
        return $this->put('/entity/product/' . $id, $data);
    }

    public function productImg($id)
    {
        return $this->get('/entity/product/' . $id . '/images');
    }

    public function demandAll()
    {
        return $this->get('/entity/demand/?limit=10');
    }

    public function demandNew()
    {
        return $this->put('/entity/demand/new', '');
    }

    public function demandCreate($data)
    {
        return $this->post('/entity/demand', $data);
    }

    public function getDemandOne($id)
    {
        return $this->get('/entity/demand/' . $id);
    }

    public function demandDelete($id)
    {
        return $this->delete('/entity/demand/' . $id);
    }

    public function demandUpdate($id, $data)
    {
        return $this->put('/entity/demand/' . $id, $data);
    }

    // hardcode
    public function demandOrganizationMeta()
    {
        return [
            'href' => 'https://online.moysklad.ru/api/remap/1.2/entity/organization/d6e2e5de-ad7a-11ea-0a80-090a00228fa2',
            'type' => 'organization',
            'uuidHref' => 'https://online.moysklad.ru/app/#mycompany/edit?id=d6e2e5de-ad7a-11ea-0a80-090a00228fa2',
            'mediaType' => 'application/json',
            'metadataHref' => 'https://online.moysklad.ru/api/remap/1.2/entity/organization/metadata'
        ];
    }

    public function addDemandPositions($id, $data)
    {
        return $this->post('/entity/demand/' . $id . '/positions', $data);
    }

    public function retaildemandNew()
    {
        return $this->put('/entity/retaildemand/new', '');
    }

    public function retaildemand()
    {
        return $this->get('/entity/retaildemand/?limit=10');
    }

    public function counterpartyNew($order)
    {
        $data = [
            'name' => Customer::fullName($order->customer),
            'phone' => $order->customer->phone,
            'email' => $order->customer->email,
            'actualAddress' => $order->np->city . ' ' . $order->np->branch,
        ];
        return $this->post('/entity/counterparty', $data);
    }

    public function counterpartyDelete($id)
    {
        return $this->delete('/entity/counterparty/' . $id);
    }

    public function attributes($options = [])
    {
        $query = [];
        if (isset($options['limit'])) {
            $query['limit'] = $options['limit'];
        }
        if (isset($options['offset'])) {
            $query['offset'] = $options['offset'];
        }
        if ($query) {
            return $this->get('/entity/variant?' . http_build_query($query));
        } else {
            return $this->get('/entity/variant');
        }
    }

    public function attribute($id)
    {
        return $this->get('/entity/variant/' . $id);
    }

    public function bundle($id)
    {
        return $this->get('/entity/bundle/' . $id);
    }

    public function assortment($q)
    {
        return $this->get('/entity/assortment?' . http_build_query(['filter' => $q]));
    }

    public function cleaning($i)
    {
        return trim($i);
    }

    /**
     * https://online.moysklad.ru/app/#company
     * https://online.moysklad.ru/app/#demand
     * https://online.moysklad.ru/app/#stockReport?reportType=GOODS
     * frontend/controllers/OrderController.php
     * src/services/InterkassaService.php
     * backend/modules/bot/telegram/TOrder.php
     * backend/modules/bot/viber/VOrder.php
     */
    public function subtractQtyProduct($order, $cart = null)
    {
        if ($cart === null) {
            $cart = new Cart();
            $cart->build(new CartData($order));
        }
        OrderMoysklad::getModel($order->id);
        if (!$order->moysklad->agent) {
            $order->moysklad->agent = $this->counterpartyNew($order);
            $order->moysklad->save(false);
        }

        $demandNew = $this->demandNew();

        $new = [];
        $new['name'] = Order::timeId($order);
        $new['organization']['meta'] = isset($demandNew['organization']['meta']) ? $demandNew['organization']['meta'] : [];
        if (empty($new['organization']['meta'])) {
            $new['organization']['meta'] = $this->demandOrganizationMeta();
        }
        $new['agent']['meta'] = $order->moysklad->agent['meta'];
        $new['store']['meta'] = $demandNew['store']['meta'];
        if (!$order->moysklad->demand) {
            $order->moysklad->demand = $this->demandCreate($new);
            $order->moysklad->save();
        }

        $positions = [];
        foreach ($cart->items as $item) {
            $product = $this->product($item['uuidProduct']);
            $positions[] = [
                'price' => $item['productPrice'] * 100,
                'quantity' => $item['qtyItem'],
                'discount' => $cart->data->discountPercent + $item['additionalDiscount'],
                'vat' => 0,
                'assortment' => [
                    'meta' => $product['meta']
                ]
            ];
        }
        if (isset($order->moysklad->demand['id'])) {
            $this->addDemandPositions($order->moysklad->demand['id'], $positions);
        }
    }

    public function leftoversDelete($order)
    {
        if (isset($order->moysklad->demand['id'])) {
            $this->demandDelete($order->moysklad->demand['id']);
            $order->moysklad->demand = null;
        }
        if (isset($order->moysklad->agent['id'])) {
            $this->counterpartyDelete($order->moysklad->agent['id']);
            $order->moysklad->agent = null;
        }
        if ($order->moysklad) {
            $order->moysklad->save(false);
        }
    }

    public function saveImg($product_id, $imgRows)
    {
        if (!$imgRows) {
            return false;
        }
        $imgIsset = ArrayHelper::map(Images::find()
            ->where(['entity_id' => $product_id])
            ->andWhere(['entity' => SHOP_PRODUCT])
            ->asArray()
            ->all(), 'img', 'id');

        $path = Yii::$app->params['imgPath'] . '/product/' . $product_id . '/';
        BaseFileHelper::removeDirectory($path);
        BaseFileHelper::createDirectory($path, 0777);

        foreach ($imgRows as $row) {
            unset($imgIsset[$row['filename']]);

            $fullPath = $path . $row['filename'];
            if (is_file($fullPath)) {
                continue;
            }
            $fp = fopen($fullPath, 'w+');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_USERPWD, $this->_login . ':' . $this->_pass);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_URL, $row['meta']['downloadHref']);
            curl_setopt($curl, CURLOPT_FILE, $fp);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            $output = curl_exec($curl);
            if ($output === false) {
                exit(curl_error($curl));
            }
            curl_close($curl);
            fclose($fp);

            ImgSave::uploadStatic($fullPath);
            $i = new Images();
            $i->entity_id = $product_id;
            $i->entity = SHOP_PRODUCT;
            $i->img = $row['filename'];
            $i->sort = 1;

            if (!$i->save()) {
                continue;
            }
            @chmod($fullPath, 0777);
        }
        if ($imgIsset) {
            foreach ($imgIsset as $nameImg => $imgId) {
                Images::deleteAll(['id' => $imgId]);
                @unlink($path . $nameImg);
            }
        }

        $imgSort = Images::find()
            ->where(['entity_id' => $product_id])
            ->andWhere(['entity' => SHOP_PRODUCT])
            ->orderBy('id asc')
            ->all();
        $x = 1;
        foreach ($imgSort as $sort) {
            $sort->sort = $x;
            $sort->save(false);
            $x++;
        }
    }
}
