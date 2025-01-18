<?php

namespace backend\modules\bot\telegram;

use backend\modules\bot\models\Logger;
use backend\modules\bot\src\traits\PaginationTrait;
use backend\modules\shop\models\Attribute;
use backend\modules\shop\models\AttributeValue;
use backend\modules\shop\models\AttributeValueProductModLink;
use backend\modules\shop\models\CustomerFilter;
use backend\modules\shop\models\ProductMod;
use backend\modules\shop\models\search\ProductModSearch;
use Yii;
use yii\helpers\ArrayHelper;

class TCatalog extends TCommon
{
    use PaginationTrait;

    /* Только для кнопки меню. Вместо метода branchQuickOrder */
    public function clickKeyboardQuickOrder()
    {

        CustomerFilter::deleteAll(['customer_id' => Yii::$app->tm->customer->id]);
        $this->session->del('attributeKeyboardByExistProduct');
        CustomerFilter::saveItemTm();
        $attribute = Attribute::bySlug('qty-of-puffs');
        $button = $this->attributeButtons($attribute, 'qtyOfPuffs1');
        $button[] = [["text" => $this->text('continue'), "callback_data" => $this->encode([
            'action' => '/TCatalog_brand',
            'continue' => true
        ])]];
        $this->delCommon();
        $this->session->set('backFromProduct', '/TCatalog_qtyOfPuffs1');
        $this->session->del('selectedProduct');
        $this->button($this->text('qtyOfPuffs'), $button);
        $this->saveSessionMessageId('mainMessageId');
        return true;
    }

    /* Только для кнопки меню. Вместо  метода branchFlavor */
    public function clickKeyboardFlavor()
    {
        $attributeSlug = 'flavor';
        $this->session->del('attributeKeyboardByExistProduct');
        CustomerFilter::deleteAll(['customer_id' => Yii::$app->tm->customer->id]);
        CustomerFilter::saveItemTm();
        $attribute = Attribute::bySlug($attributeSlug);
        $button = $this->attributeButtons($attribute, $attributeSlug);
        $button[] = [
            ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCatalog_branchFlavor'])],
            ["text" => $this->text('continue'), "callback_data" => $this->encode(['action' => '/TCatalog_productsFilter'])]
        ];
        $this->session->set('backFromProduct', '/TCatalog_flavor');
        $this->delCommon();
        $this->button($this->text($attributeSlug), $button);
        $this->saveSessionMessageId('mainMessageId');
        return true;
    }

    public function branchQuickOrder()
    {
        CustomerFilter::deleteAll(['customer_id' => Yii::$app->tm->customer->id]);
        $this->session->del('attributeKeyboardByExistProduct');
        $this->qtyOfPuffs1();
    }

    public function qtyOfPuffs1()
    {
        CustomerFilter::saveItemTm();
        $attribute = Attribute::bySlug('qty-of-puffs');
        $button = $this->attributeButtons($attribute, 'qtyOfPuffs1');
        $button[] = [
            ["text" => $this->text('continue'), "callback_data" => $this->encode(['action' => '/TCatalog_brand'])]
        ];
        $messageId = $this->session->messageId('mainMessageId');
        $this->delCommon();

        if ($messageId) {
            $this->edit($this->text('qtyOfPuffs'), $button, $messageId);
        } else {
            $this->button($this->text('qtyOfPuffs'), $button);
            $this->saveSessionMessageId('mainMessageId');
        }
    }

    public function brand()
    {
        CustomerFilter::saveItemTm();
        $attribute = Attribute::bySlug('brand');
        Logger::commit($attribute);
        $button = $this->attributeButtons($attribute, 'brand');
        $button[] = [
            ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCatalog_qtyOfPuffs1'])],
            ["text" => $this->text('continue'), "callback_data" => $this->encode(['action' => '/TCatalog_productsFilter', 'continue' => true])]
        ];
        $messageId = $this->session->messageId('mainMessageId');
        $this->delCommon();

        $this->session->set('backFromProduct', '/TCatalog_brand');
        if ($messageId) {
            $this->edit($this->text('brandText'), $button, $messageId);
        } else {
            $this->button($this->text('brandText'), $button);
            $this->saveSessionMessageId('mainMessageId');
        }
    }

    public function draftTemperature()
    {
        CustomerFilter::saveItemTm();
        $attribute = Attribute::bySlug('draft-temperature');
        $button = $this->attributeKeyboardByExistProduct($attribute, 'draftTemperature');
        $button[] = [
            ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCatalog_branchQuickOrder'])],
            ["text" => $this->text('continue'), "callback_data" => $this->encode([
                'action' => '/TCatalog_aftertaste',
                'continue' => true
            ])]
        ];
        $messageId = $this->session->messageId('mainMessageId');
        $this->delCommon();
        if ($messageId) {
            $this->edit($attribute->name, $button, $messageId);
        } else {
            $this->button($attribute->name, $button);
            return $this->saveSessionMessageId('mainMessageId');
        }
    }

    public function aftertaste()
    {
        CustomerFilter::saveItemTm();
        $attribute = Attribute::bySlug('aftertaste');
        $button = $this->attributeKeyboardByExistProduct($attribute, 'aftertaste');
        $button[] = [
            ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCatalog_draftTemperature'])],
            ["text" => $this->text('continue'), "callback_data" => $this->encode(['action' => '/TCatalog_productsFilter'])]
        ];
        $this->session->set('backFromProduct', '/TCatalog_aftertaste');
        $messageId = $this->session->messageId('mainMessageId');
        if ($messageId) {
            $this->edit($attribute->name, $button, $messageId);
        } else {
            $this->delCommon();
            $this->button($attribute->name, $button);
            return $this->saveSessionMessageId('mainMessageId');
        }
    }

    /**
     * branchFlavor
     */
    public function branchFlavor()
    {
        $this->session->del('attributeKeyboardByExistProduct');
        CustomerFilter::deleteAll(['customer_id' => Yii::$app->tm->customer->id]);
        $this->flavor();
    }

    public function flavor()
    {
        CustomerFilter::saveItemTm();
        $attribute = Attribute::bySlug('flavor');
        $button = $this->attributeButtons($attribute, 'flavor');
        $button[] = [
            ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCatalog_branchFlavor'])],
            ["text" => $this->text('continue'), "callback_data" => $this->encode(['action' => '/TCatalog_productsFilter'])]
        ];
        $this->session->set('backFromProduct', '/TCatalog_flavor');
        $messageId = $this->session->messageId('mainMessageId');
        if ($messageId) {
            $this->edit($attribute->name, $button, $messageId);
        } else {
            $this->delCommon();
            $this->button($attribute->name, $button);
            return $this->saveSessionMessageId('mainMessageId');
        }
    }

    public function qtyOfPuffs2()
    {
        CustomerFilter::saveItemTm();
        $attribute = Attribute::bySlug('qty-of-puffs');
        $button = $this->attributeButtons($attribute, 'qtyOfPuffs2');
        $button[] = [["text" => $this->text('continue'), "callback_data" => $this->encode([
            'action' => '/TCatalog_flavor'
        ])]];
        $messageId = $this->session->messageId('mainMessageId');
        $this->delCommon();
        if ($messageId) {
            $this->edit($attribute->name, $button, $messageId);
        } else {
            $this->button($attribute->name, $button);
            return $this->saveSessionMessageId('mainMessageId');
        }
    }

    private function attributeButtons($attribute, $action)
    {
        $attributeValueIdFilter = CustomerFilter::attributeValueId(Yii::$app->tm->customer->id);
        $filterNames = [];
        foreach ($attributeValueIdFilter as $attributeId => $attributeValues) {
            if ($attributeId == $attribute->id) {
                continue;
            }
            foreach ($attributeValues as $attributeValue) {
                $filterNames[] = AttributeValue::findOne($attributeValue)->name;
            }
        }
        $b = [];
        /** @var AttributeValue $attributeValue */
        foreach ($attribute->attributeValue as $attributeValue) {
            $dataProvider = ProductModSearch::searchProductQuery($filterNames,  $attributeValue->name);
            if ($dataProvider->count() == 0) {
                continue;
            }
            $b[][] = ['text' => $this->checkAttribute($attributeValue->id, $attributeValueIdFilter) . $attributeValue->name,
                'callback_data' => $this->encode([
                    'action' => '/TCatalog_' . $action,
                    'av_id' => $attributeValue->id
                ])];
        }
        return $b;
    }

    private function attributeKeyboardByExistProduct($attribute, $action)
    {
        $attributeValueIdFilter = CustomerFilter::attributeValueId(Yii::$app->tm->customer->id);
        $dataProvider = ProductModSearch::searchBot($attributeValueIdFilter);
        $attributeValueId = ArrayHelper::getColumn($attribute->attributeValue, 'id');
        if (isset(Yii::$app->tm->data->continue)) {
            $items = AttributeValueProductModLink::attributeByMod($dataProvider->getKeys(), $attributeValueId);
            $this->session->set('attributeKeyboardByExistProduct', $items);
        } else {
            $items = $this->session->get('attributeKeyboardByExistProduct');
        }
        $b = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                $b[][] = ['text' => $this->checkAttribute($item['attributeValue']['id'], $attributeValueIdFilter) . $item['attributeValue']['name'],
                    'callback_data' => $this->encode([
                        'action' => '/TCatalog_' . $action,
                        'av_id' => $item['attributeValue']['id']
                    ])];
            }
        }
        return $b;
    }

    private function checkAttribute($av_id, $attributeValueIdFilter)
    {
        foreach ($attributeValueIdFilter as $item) {
            if (ArrayHelper::isIn($av_id, $item)) {
                return $this->check();
            }
        }
    }

    /**
     * @param array $models
     */
    protected function buildProductsButtons(array $models)
    {
        $messageId = $this->session->messageId('mainMessageId');
        if ($models) {
            $button = $this->productButtons($models);
            if ($this->_totalCount > 10) {
                $page = $this->_page ? $this->_page : $this->session->get('page');
                if (!$page) {
                    $page = 1;
                }
                $buttonPagination[] = [
                    ["text" => '«', "callback_data" => $this->encode(['action' => $this->session->get('prevPaginationAction')])],
                    ["text" => $page . '/' . $this->_totalCountPage, "callback_data" => 'none'],
                    ["text" => '»', "callback_data" => $this->encode(['action' => $this->session->get('nextPaginationAction')])],
                ];
                $button = array_merge($button, $buttonPagination);
            }
            $button = $this->getBottomButtons($button);
        } else {
            $button = $this->getBottomButtons();
        }

        if ($this->cart->items) {
            $cartText = $this->cart->cartTextTm() . PHP_EOL;
        } else {
            $cartText = '';
        }

        if (!is_null($messageId)) {
            $this->deleteMessageByMessageId($messageId);
            $this->button($cartText . $this->text('foundProduct'), $button);
//            $this->edit($cartText . $this->text('foundProduct'), $button, $messageId);
            $this->saveSessionMessageId('mainMessageId');
        } else {
            $this->button($cartText . $this->text('foundProduct'), $button);
            $this->saveSessionMessageId('mainMessageId');
        }
    }

    /**
     * @param array $button
     * @return array
     */
    protected function getBottomButtons(array $button = []): array
    {
        if ($this->session->get('backFromProduct') != 'without back button' && $this->cart->items) {
            $button[] = [
                ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => $this->session->get('backFromProduct')])],
                ["text" => $this->text('cartButton'), "callback_data" => $this->encode(['action' => '/TCart_menuCart'])]
            ];
        } elseif ($this->session->get('backFromProduct') != 'without back button') {
            $button[] = [
                ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => $this->session->get('backFromProduct')])],
            ];
        } elseif ($this->cart->items) {
            $button[] = [
                ["text" => $this->text('cartButton'), "callback_data" => $this->encode(['action' => '/TCart_menuCart'])]
            ];
        }
        return $button;
    }

    private function query()
    {
        $attributeValueId = CustomerFilter::attributeValueId(Yii::$app->tm->customer->id);
        $dataProvider = ProductModSearch::searchBot($attributeValueId);
        $totalCountPage = $dataProvider->totalCount / $this->_perPage;
        $this->_totalCountPage = ceil($totalCountPage);
        $this->_totalCount = $dataProvider->totalCount;
        return ProductMod::find()
            ->where(['id' => $dataProvider->getKeys()])
            ->with(['product.img']);
    }

    public function productsFilter()
    {
        $this->setOrder();
        $this->setOrderItem();
        $this->setCart();
        $this->session->set('backToQueryResult', 'productsFilter');
        if ($this->session->get('page')) {
            $this->_page = $this->session->get('page');
        }
        $models = $this->getProduct();
        $this->buildProductsButtons($models);
    }

    public function searchProductsInit()
    {
        $this->session->set('page', 1);
        $this->session->del('mainMessageId');
        $this->searchProducts();
    }

    public function searchProducts()
    {
        $this->setOrder();
        $this->setOrderItem();
        $this->setCart();
        $this->session->set('backToQueryResult', 'searchProducts');
        $searchWords = $this->session->get('customerSearch');
        if ($searchWords === null) {
            $searchWords = Yii::$app->tm->data->value;
            $this->session->set('customerSearch', $searchWords);
        } elseif (isset(Yii::$app->tm->data->value) && $searchWords != Yii::$app->tm->data->value) {
            $this->session->set('customerSearch', Yii::$app->tm->data->value);
            $searchWords = $this->session->get('customerSearch');
        }

        $this->session->set('backFromProduct', 'without back button');
        $this->session->set('nextPaginationAction', '/TCatalog_nextSearchPage');
        $this->session->set('prevPaginationAction', '/TCatalog_prevSearchPage');
        $this->session->set('backToQueryResult', 'searchProducts');
        $models = $this->getSearchProducts($searchWords);
        $this->buildProductsButtons($models);
    }

    private function productButtons($models)
    {
        $button = [];
        foreach ($models as $mod) {
            list($selected, $productAction) = $this->getSelectAndAction($mod);
            $checkSign = $this->getCheckSign($mod);
            // product view
            $button[][] = ['text' => $checkSign . $mod->product->name, 'callback_data' => json_encode(['action' => '/TCatalog_product', 'mod_id' => $mod->id])];
            $this->session->set('deleteMessage', false);
            // buttons +, -, x, info view
//            if ($this->_orderItem) {
//                $button[][] = ['text' => $checkSign . $mod->product->name, 'callback_data' => json_encode(['action' => '/TCatalog_' . $productAction, 'mod_id' => $mod->id])];
//                if ($selected) {
//                    $button[] = [["text" => $this->infoProductQtyPrice(), "callback_data" => 'none']];
//                    $button[] = [
//                        ["text" => 'x', "callback_data" => $this->encode(['action' => '/TCatalog_delete', 'mod_id' => $mod->id, 'order_id' => $this->_orderItem->order_id])],
//                        ["text" => '-', "callback_data" => $this->encode(['action' => '/TCatalog_minus', 'mod_id' => $mod->id, 'order_id' => $this->_orderItem->order_id])],
//                        ["text" => '+', "callback_data" => $this->encode(['action' => '/TCatalog_plus', 'mod_id' => $mod->id, 'order_id' => $this->_orderItem->order_id])],
//                        ["text" => 'info', "callback_data" => $this->encode(['action' => '/TCatalog_product', 'mod_id' => $mod->id, 'order_id' => $this->_orderItem->order_id])],
//                    ];
//                }
//            } else {
//                if ($selected) {
//                    $button[][] = ['text' => "👉 ".$checkSign . $mod->product->name." 👈",
//                        'callback_data' => json_encode(['action' => '/TCatalog_' . $productAction, 'mod_id' => $mod->id])
//                    ];
//
//                    if ($this->cart->data->discountPercent) {
//                        $priceItemProductTotal = $this->cart->percent($mod->price);
//                    } else {
//                        $priceItemProductTotal = $mod->price;
//                    }
//                    $button[] = [
//                        ["text" => 'Цена: ' . $this->cart->showPrice($priceItemProductTotal), "callback_data" => 'none'],
//                    ];
//                    $button[] = [
//                        ["text" => 'info', "callback_data" => $this->encode(['action' => '/TCatalog_product', 'mod_id' => $mod->id])],
//                        ["text" => $this->text('buttonAddToCartProduct'), "callback_data" => $this->encode(['action' => '/TCatalog_add', 'mod_id' => $mod->id])],
//                    ];
//                } else {
//                    $button[][] = ['text' => $checkSign . $mod->product->name, 'callback_data' => json_encode(['action' => '/TCatalog_' . $productAction, 'mod_id' => $mod->id])];
//
//                }
//            }
        }
        return $button;
    }

    public function backToQueryResult()
    {
        $method = $this->session->get('backToQueryResult');
        if ($this->session->get('page')) {
            $this->_page = $this->session->get('page');
        }
        return $this->$method();
    }

    public function selectProduct()
    {
        $this->session->set('selectedProduct', Yii::$app->tm->data->mod_id);
        return $this->backToQueryResult();
    }

    public function unselectProduct()
    {
        $this->session->del('selectedProduct');
        return $this->backToQueryResult();
    }

    public function product()
    {
        $this->setOrder();
        $this->setOrderItem();
        $this->setCart();
        $messageId = $this->session->messageId('mainMessageId');
        $productData = ProductMod::productData(ProductMod::byId(Yii::$app->tm->data->mod_id));
        $this->session->set('selectedProduct', Yii::$app->tm->data->mod_id);
        $this->setTextProduct($productData);
        if ($this->_orderItem) {
            if ($this->session->get('deleteMessage')) {
                $this->session->del('deleteMessage');
                $this->deleteMessageByMessageId($messageId);
                $this->button($this->text, $this->keyboardProductCart());
                $this->saveSessionMessageId('mainMessageId');
            } else {
//                $this->edit($this->text, $this->keyboardProductCart(), $messageId);
                if ($productMessageId = $this->session->getSessionMessageId('productMessageId')) {
                    $this->deleteMessageByMessageId($productMessageId);
                }
                $this->button($this->text, $this->keyboardProductCart());
                $this->saveSessionMessageId('productMessageId');
            }
        } else {
            $button[] = [[
                "text" => $this->text('buttonAddToCartProduct'),
                "callback_data" => $this->encode(['action' => '/TCatalog_add', 'mod_id' => $productData['mod_id']])
            ]];
            $button[] = [[
                "text" => $this->text('backToFoundProducts'),
                "callback_data" => $this->encode(['action' => '/TCatalog_backToQueryResult'])
            ]];
            if ($this->session->get('deleteMessage')) {
                $this->session->del('deleteMessage');
                $this->deleteMessageByMessageId($messageId);
                $this->button($this->text, $button);
                $this->saveSessionMessageId('mainMessageId');
            } else {
//                $this->edit($this->text, $button, $messageId);
                if ($productMessageId = $this->session->getSessionMessageId('productMessageId')) {
                    $this->deleteMessageByMessageId($productMessageId);
                }
                $this->button($this->text, $button);
                $this->saveSessionMessageId('productMessageId');
            }
        }
    }

    public function productReferral()
    {
        $this->setCart();
        $this->setOrderItem();
        $this->mainMenu($this->text('start'));
        $referralInput = $this->session->get('referralInput');
        $mod = ProductMod::byProductCode($referralInput['productCode']);
        if ($mod) {
            $productData = ProductMod::productData($mod);
            $this->setTextProduct($productData);
            $button[] = [["text" => $this->text('buttonAddToCartProduct'), "callback_data" => $this->encode(['action' => '/TCatalog_add', 'mod_id' => $productData['mod_id']])]];
            $button[] = [["text" => $this->text('backToFoundProducts'), "callback_data" => $this->encode(['action' => '/TCatalog_productsFilter'])]];
            $this->button($this->text, $button);
            $this->saveSessionMessageId('mainMessageId');
        }
        $this->session->del('referralInput');
    }

    private function keyboardProductCart()
    {
        $button[] = [["text" => $this->infoProductQtyPrice(), "callback_data" => 'none']];
        $button[] = [
            ["text" => 'x', "callback_data" => $this->encode(['action' => '/TCatalog_delete', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id])],
            ["text" => '-', "callback_data" => $this->encode(['action' => '/TCatalog_minus', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id])],
            ["text" => '+', "callback_data" => $this->encode(['action' => '/TCatalog_plus', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id])]
        ];
        $button[] = [["text" => $this->text('backToFoundProducts'), "callback_data" => $this->encode(['action' => '/TCatalog_backToQueryResult'])]];
        $button[] = [["text" => $this->text('cartButton'), "callback_data" => $this->encode(['action' => '/TCart_menuCart'])]];
        return $button;
    }

    public function add()
    {
        parent::add();
        $this->session->set('selectedProduct', $this->_orderItem->mod_id);
        $this->product();
//        return $this->backToQueryResult();
    }

    public function plus()
    {
        parent::plus();
        $this->session->set('selectedProduct', $this->_orderItem->mod_id);
        $this->product();
//        return $this->backToQueryResult();
    }

    public function minus()
    {
        parent::minus();
        $this->session->set('selectedProduct', $this->_orderItem->mod_id);
        $this->product();
//        return $this->backToQueryResult();
    }

    public function delete()
    {
        parent::delete();
        $messageId = $this->session->get('selectedProduct');
        $this->deleteMessageByMessageId($messageId);
        $this->session->del('selectedProduct');
        return $this->backToQueryResult();
    }

    private function queryByWordsInNames(): array
    {
        $models = [];
        $searchWords = explode(',', Yii::$app->tm->data->value);
        foreach ($searchWords as $searchWord) {
            $searchResult = ProductMod::byName($searchWord);
            if ($searchResult != null) {
                $models[$searchWord] = $searchResult;
            }
        }
        return $models;
    }

    private function getSearchProducts($searchWords): array
    {
        $searchWordsArr = explode(',', $searchWords);
        $query = ProductMod::searchProductQuery($searchWordsArr);
        $this->_totalCount = count($query->all());
        $totalCountPage = $this->_totalCount / $this->_perPage;
        $this->_totalCountPage = ceil($totalCountPage);
        return $this->setPaginationByQuery($query);
    }

    /**
     * @param $mod
     * @return array
     */
    private function getSelectAndAction($mod): array
    {
        if (($selected = $this->session->get('selectedProduct')) && $selected == $mod->id) {
            $selected = true;
            $this->session->del('selectedProduct');
            $productAction = 'unselectProduct';
        } else {
            $selected = false;
            $productAction = 'selectProduct';
        }
        return array($selected, $productAction);
    }

    /**
     * @param $mod
     * @return string
     */
    private function getCheckSign($mod): string
    {
        if (isset($this->cart->items[$mod->id])) {
            $checkSign = $this->check();
        } else {
            $checkSign = '';
        }
        return $checkSign;
    }
}
