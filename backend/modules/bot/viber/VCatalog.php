<?php

namespace backend\modules\bot\viber;

use backend\modules\bot\src\traits\PaginationTrait;
use backend\modules\media\models\Img;
use backend\modules\shop\models\Attribute;
use backend\modules\shop\models\AttributeValueProductModLink;
use backend\modules\shop\models\Category;
use backend\modules\shop\models\CustomerFilter;
use backend\modules\shop\models\ProductMod;
use backend\modules\shop\models\search\ProductModSearch;
use frontend\models\cart\Cart;
use Yii;
use yii\helpers\ArrayHelper;

class VCatalog extends VCommon
{
    use PaginationTrait;

    public function branchQuickOrder()
    {
        CustomerFilter::deleteAll(['customer_id' => Yii::$app->vb->customer->id]);
        $this->session->del('attributeKeyboardByExistProduct');
        $this->qtyOfPuffs1();
    }

    public function qtyOfPuffs1()
    {
        CustomerFilter::saveItemVb();
        $attribute = Attribute::bySlug('qty-of-puffs');
        $keyboard[] = $this->buttonImg($this->text('continue'), '/img/vb/prodol-min.jpg', [
            'action' => 'VCatalog_draftTemperature',
            'continue' => true
        ]);
        $keyboard = array_merge($keyboard, $this->attributeKeyboard($attribute, 'VCatalog_qtyOfPuffs1'));
        $keyboard[] = $this->buttonMainMenu();
        $this->keyboard($attribute->name, $keyboard);
    }

    public function draftTemperature()
    {
        CustomerFilter::saveItemVb();
        $attribute = Attribute::bySlug('draft-temperature');
        $keyboard[] = $this->buttonImg('back', '/img/vb/nazad-3-min.jpg', ['action' => 'VCatalog_qtyOfPuffs1'], 3);
        $keyboard[] = $this->buttonImg('continue', '/img/vb/prodol-3-min.jpg', [
            'action' => 'VCatalog_aftertaste',
            'continue' => true
        ], 3);
        $keyboard = array_merge($keyboard, $this->attributeKeyboardByExistProduct($attribute, 'VCatalog_draftTemperature'));
        $keyboard[] = $this->buttonMainMenu();
        $this->keyboard($attribute->name, $keyboard);
    }

    public function aftertaste()
    {
        $this->session->set('backFromProduct', 'VCatalog_aftertaste');
        CustomerFilter::saveItemVb();
        $attribute = Attribute::bySlug('aftertaste');
        $keyboard[] = $this->buttonImg('back', '/img/vb/nazad-3-min.jpg', ['action' => 'VCatalog_draftTemperature'], 3);
        $keyboard[] = $this->buttonImg('continue', '/img/vb/prodol-3-min.jpg', ['action' => 'VCatalog_productsFilter'], 3);
        $keyboard = array_merge($keyboard, $this->attributeKeyboardByExistProduct($attribute, 'VCatalog_aftertaste'));
        $this->keyboard($attribute->name, $keyboard);
    }

    /**
     * branchFlavor
     */
    public function branchFlavor()
    {
        $this->session->del('attributeKeyboardByExistProduct');
        CustomerFilter::deleteAll(['customer_id' => Yii::$app->vb->customer->id]);
        $this->flavor();
    }

    public function flavor()
    {
        $this->session->set('backFromProduct', 'VCatalog_flavor');
        CustomerFilter::saveItemVb();
        $attribute = Attribute::bySlug('flavor');
        $keyboard[] = $this->buttonImg('back', '/img/vb/nazad-3-min.jpg', ['action' => 'VCatalog_qtyOfPuffs2'], 3);
        $keyboard[] = $this->buttonImg('continue', '/img/vb/prodol-3-min.jpg', ['action' => 'VCatalog_productsFilter'], 3);
        $keyboard = array_merge($keyboard, $this->attributeKeyboard($attribute, 'VCatalog_flavor'));
        $this->keyboard($attribute->name, $keyboard);
    }

    public function qtyOfPuffs2()
    {
        CustomerFilter::saveItemVb();
        $attribute = Attribute::bySlug('qty-of-puffs');
        $keyboard[] = $this->buttonImg($this->text('continue'), '/img/vb/prodol-min.jpg', ['action' => 'VCatalog_flavor']);
        $keyboard = array_merge($keyboard, $this->attributeKeyboard($attribute, 'VCatalog_qtyOfPuffs2'));
        $keyboard[] = $this->buttonMainMenu();
        $this->keyboard($attribute->name, $keyboard);
    }

    public function attributeKeyboard($attribute, $action)
    {
        $attributeValueIdFilter = CustomerFilter::attributeValueId(Yii::$app->vb->customer->id);
        $keyboard = [];
        $x = 0;
        foreach ($attribute->attributeValue as $attributeValue) {
            $keyboard[] = $this->buttonImgText($this->checkAttribute($attributeValue->id, $attributeValueIdFilter) . $attributeValue->name, '/img/vb/empty-min.jpg', [
                'action' => $action,
                'attribute_value_id' => $attributeValue->id
            ]);
            if ($x == 20) {
                break;
            }
            $x++;
        }
        return $keyboard;
    }

    public function attributeKeyboardByExistProduct($attribute, $action)
    {
        $attributeValueIdFilter = CustomerFilter::attributeValueId(Yii::$app->vb->customer->id);
        $dataProvider = ProductModSearch::searchBot($attributeValueIdFilter);
        $attributeValueId = ArrayHelper::getColumn($attribute->attributeValue, 'id');

        if (isset(Yii::$app->vb->data->continue)) {
            $items = AttributeValueProductModLink::attributeByMod($dataProvider->getKeys(), $attributeValueId);
            $this->session->set('attributeKeyboardByExistProduct', $items);
        } else {
            $items = $this->session->get('attributeKeyboardByExistProduct');
        }
        $keyboard = [];
        foreach ($items as $item) {
            $keyboard[] = $this->buttonImgText($this->checkAttribute($item['attributeValue']['id'], $attributeValueIdFilter) . $item['attributeValue']['name'], '/img/vb/empty-min.jpg', [
                'action' => $action,
                'attribute_value_id' => $item['attributeValue']['id']
            ]);
        }
        return $keyboard;
    }

    public function checkAttribute($attribute_value_id, $attributeValueIdFilter)
    {
        foreach ($attributeValueIdFilter as $item) {
            if (ArrayHelper::isIn($attribute_value_id, $item)) {
                return $this->check();
            }
        }
    }

    private function query()
    {
        $attributeValueId = CustomerFilter::attributeValueId(Yii::$app->vb->customer->id);
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
        $models = $this->getProduct();
        if ($models) {
            if ($this->_totalCount > 10) {
                $keyboard[] = $this->buttonImg('«', '/img/vb/left-min.jpg', ['action' => 'VCatalog_prev'], 1);
                $keyboard[] = $this->buttonImg($this->text('back'), '/img/vb/nazad_2-3-min.jpg', ['action' => $this->session->get('backFromProduct')], 4);
                $keyboard[] = $this->buttonImg('»', '/img/vb/right-min.jpg', ['action' => 'VCatalog_next'], 1);
            } else {
                $keyboard[] = $this->buttonImg($this->text('back'), '/img/vb/nazad-min.jpg', ['action' => $this->session->get('backFromProduct')]);
            }

            $keyboard = array_merge($keyboard, $this->productsKeyboard($models));
            if ($this->_totalCount > 10) {
                $keyboard[] = $this->buttonImg('«', '/img/vb/left-min.jpg', ['action' => 'VCatalog_prev'], 1);
                $keyboard[] = $this->buttonMainMenu4();
                $keyboard[] = $this->buttonImg('»', '/img/vb/right-min.jpg', ['action' => 'VCatalog_next'], 1);
            } else {
                $keyboard[] = $this->buttonMainMenu();
            }

            $this->keyboard($this->text('foundProduct'), $keyboard);
        } else {
            $keyboard[] = $this->buttonImg($this->text('back'), '/img/vb/nazad-min.jpg', ['action' => 'VCatalog_aftertaste']);
            $keyboard[] = $this->buttonMainMenu();
            $this->keyboard($this->text('notFound'), $keyboard);
        }
    }

    public function category()
    {
        foreach (Category::find()->where(['status' => Category::STATUS_ACTIVE])->all() as $category) {
            $keyboard[] = $this->buttonImgText($category->name, '/img/vb/empty-min.jpg', ['action' => 'VCatalog_products', 'category_id' => $category->id]);
        }
        $keyboard[] = $this->buttonMainMenu();
        $this->keyboard($this->text('category'), $keyboard);
    }

    public function products()
    {
        $keyboard = $this->productsKeyboard(ProductMod::byCategory(Yii::$app->vb->data->category_id));
        $keyboard[] = $this->button('Каталог', ['action' => 'VCatalog_category']);
        $this->keyboard($this->text('products'), $keyboard);
    }

    public function product()
    {
        $this->setCart();
        $this->setOrderItem();
        $productData = ProductMod::productData(ProductMod::byId(Yii::$app->vb->data->mod_id), 'viber');
        $this->setTextProduct($productData);
        if ($this->_orderItem) {
            $keyboard = $this->keyboardProductCart();
        } else {
            $keyboard[] = $this->buttonImg('Добавить в корзину', '/img/vb/add_korz-min.jpg', ['action' => 'VCatalog_add', 'mod_id' => $productData['mod_id']]);
            $keyboard[] = $this->buttonImg('backToFoundProducts', '/img/vb/back-min.jpg', ['action' => 'VCatalog_productsFilter']);
        }
        if (strlen($this->text) < 120) {
            return $this->sendPictureKeyboard($this->text, $keyboard, $productData['img']);
        } else {
            $this->sendPicture('.', $productData['img']);
            return $this->keyboard($this->text, $keyboard);
        }
    }

    public function productReferral()
    {
        $this->setCart();
        $this->setOrderItem();
        $referralInput = $this->session->get('referralInput');
        $mod = ProductMod::byProductCode($referralInput['productCode']);
        $productData = ProductMod::productData($mod, 'viber');
        $this->setTextProduct($productData);
        $keyboard[] = $this->buttonImg('Добавить в корзину', '/img/vb/add_korz-min.jpg', ['action' => 'VCatalog_add', 'mod_id' => $productData['mod_id']]);
        $keyboard[] = $this->buttonMainMenu();
        if (strlen($this->text) < 120) {
            $this->sendPictureKeyboard($this->text, $keyboard, $productData['img']);
        } else {
            $this->sendPicture('.', $productData['img']);
            $this->keyboard($this->text, $keyboard);
        }
        $this->session->del('referralInput');
    }

    private function keyboardProductCart()
    {
        $keyboard[] = $this->buttonNoneText($this->infoProductQtyPrice(), '/img/vb/empty-min.jpg');
        $keyboard[] = $this->buttonImg('x', '/img/vb/del-min.jpg', ['action' => 'VCatalog_delete', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id], 2);
        $keyboard[] = $this->buttonImg('-', '/img/vb/minus-min.jpg', ['action' => 'VCatalog_minus', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id], 2);
        $keyboard[] = $this->buttonImg('+', '/img/vb/pluse-min.jpg', ['action' => 'VCatalog_plus', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id], 2);
        $keyboard[] = $this->buttonImg('cartButton', '/img/vb/korz-min.jpg', ['action' => 'VCart_menuCart']);
        $keyboard[] = $this->buttonImg('backToFoundProducts', '/img/vb/back-min.jpg', ['action' => 'VCatalog_productsFilter']);
        return $keyboard;
    }

    private function productsKeyboard($mods)
    {
        foreach ($mods as $mod) {
            $img = Img::iVb($mod->product->img, Yii::$app->params['sizeProduct']['mid_square']);
            $this->text .= $mod->product->name . '<br><br>';
            $this->text .= Cart::showPriceStatic($mod->product->price);
            $keyboard[] = $this->buttonImg($mod->product->name, $img, ['action' => 'VCatalog_product', 'mod_id' => $mod->id], 2, 2);
            $keyboard[] = $this->buttonImgText($this->text, '/img/vb/plashka_2-3_2-min.jpg', ['action' => 'VCatalog_product', 'mod_id' => $mod->id], 4, 2);
            $this->text = '';
        }
        return $keyboard;
    }

    public function add()
    {
        parent::add();
        $this->product();
    }

    public function plus()
    {
        parent::plus();
        $this->product();
    }

    public function minus()
    {
        parent::minus();
        $this->product();
    }

    public function delete()
    {
        parent::delete();
        return $this->productsFilter();
    }


}
