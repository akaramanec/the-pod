<?php

namespace src\parser;

use backend\modules\bot\models\BotLogger;
use backend\modules\shop\models\Attribute;
use backend\modules\shop\models\AttributeValue;
use backend\modules\shop\models\AttributeValueProductModLink;
use backend\modules\shop\models\Product;
use backend\modules\shop\models\ProductMod;
use backend\modules\shop\models\ProductModTemp;
use Yii;
use yii\helpers\Inflector;

class ProductModAndAttribute
{
    public $api;
    private $_slugAddNumber = 1;

    public function __construct($api)
    {
        $this->api = $api;
        Yii::$app->db->createCommand("TRUNCATE TABLE shop_attribute_value_product_mod_link")->execute();
        $this->setStatus();
        $this->start();
        $this->saveAttributeCache();
        $this->deleteEmpty();
    }

    public function start()
    {
        foreach (ProductModTemp::find()->each() as $item) {
            $mod = $this->getProductMod($item->uuid);
            if ($mod->isNewRecord) {
                $mod->uuid = $item->uuid;
                $mod->slug = $this->generateSlugProductMod($item->slug);
                $mod->code = $item->code;
            }
            $mod->product_id = $item->product_id;
            $mod->name = $item->name;
            $mod->price = $item->price / 100;
            $mod->description = $item->description;
            $mod->status = ProductMod::STATUS_ACTIVE;

            if (!$mod->save()) {
                BotLogger::save_input(['ProductModAndAttribute start', $mod->errors]);
            }
            if ($mod->status == ProductMod::STATUS_ACTIVE) {
                $this->characteristics($item->characteristics, $mod->id);
            }
        }
    }

    public function saveAttributeCache()
    {
        foreach (Product::find()->each() as $product) {
            $p = new Product();
            $p->saveAttributeCache($product);
        }
    }

    public function deleteEmpty()
    {
        $attributeValue = AttributeValue::find();
        foreach ($attributeValue->each() as $av) {
            if (empty($av->attributeValueProductModLink)) {
                $av->delete();
            }
        }
        $attributes = Attribute::find();
        foreach ($attributes->each() as $attribute) {
            if (empty($attribute->attributeValue)) {
                $attribute->delete();
            }
        }
    }

    public function characteristics($characteristics, $mod_id)
    {
        foreach ($characteristics as $item) {
            $attribute_id = $this->attributeSave($item);
            $attribute_value_id = $this->attributeValueSave($item, $attribute_id);
            $this->attributeValueProductModLinkSave($attribute_value_id, $mod_id);
        }
    }

    public function attributeValueProductModLinkSave($attribute_value_id, $mod_id)
    {
        $avpml = new AttributeValueProductModLink();
        $avpml->attribute_value_id = $attribute_value_id;
        $avpml->mod_id = $mod_id;
        $avpml->save();
    }

    public function attributeValueSave($item, $attribute_id)
    {
        $av = $this->getAttributeValue($item['value']);
        if ($av->isNewRecord) {
            $av->attribute_id = $attribute_id;
        }
        $av->name = $item['value'];
        if (!$av->save()) {
            BotLogger::save_input(['ProductModAndAttribute attributeValueSave', $av->errors]);
        }
        return $av->id;
    }

    public function attributeSave($item)
    {
        $attribute = $this->getAttribute($item['id']);
        if ($attribute->isNewRecord) {
            $attribute->uuid = $item['id'];
            $attribute->status = Attribute::STATUS_ACTIVE;
            $attribute->type = Attribute::TYPE_STRING;
            $attribute->slug = $this->generateSlug($item['name']);
        }
        $attribute->name = $item['name'];

        if (!$attribute->save()) {
            BotLogger::save_input(['ProductModAndAttribute attributeSave', $attribute->errors]);
        }
        return $attribute->id;
    }

    public function getAttributeValue($name)
    {
        if ($av = AttributeValue::byName($name)) {
            return $av;
        }
        return new AttributeValue();
    }

    public function getAttribute($uuid)
    {
        if ($attribute = Attribute::byUuid($uuid)) {
            return $attribute;
        }
        return new Attribute();
    }

    public function getProductMod($uuid)
    {
        if ($product = ProductMod::getByUuid($uuid)) {
            return $product;
        }
        return new ProductMod();
    }

    private function generateSlug($name)
    {
        $slug = Inflector::slug($name);
        $model = Attribute::find()->where(['slug' => $slug])->limit(1)->one();
        if ($model) {
            $newSlug = $slug . $this->_slugAddNumber++;
            return $this->generateSlug($newSlug);
        } else {
            $this->_slugAddNumber = 1;
            return $slug;
        }
    }

    private function generateSlugProductMod($name)
    {
        $slug = Inflector::slug($name);
        $model = ProductMod::find()->where(['slug' => $slug])->limit(1)->one();
        if ($model) {
            $newSlug = $slug . $this->_slugAddNumber++;
            return $this->generateSlugProductMod($newSlug);
        } else {
            $this->_slugAddNumber = 1;
            return $slug;
        }
    }

    public function setStatus()
    {
        foreach (ProductMod::find()->each() as $mod) {
            if (ProductModTemp::find()->where(['uuid' => $mod->uuid])->limit(1)->exists()) {
                continue;
            }
            $mod->status = Product::STATUS_INACTIVE;
            $mod->save(false);
        }
    }
}
