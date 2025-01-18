<?php

namespace frontend\widgets;

use backend\modules\shop\models\AttributeValueProductModLink;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class FilterWidget extends Widget
{
    public $mod_id;
    public $searchModel;
    public $slug;
    public $priceMaxMin;
    public $checked = [];

    public function run()
    {
        $avpml = AttributeValueProductModLink::find()
            ->alias('avpml')
            ->joinWith(['attributeValue AS attributeValue', 'attributeValue.shopAttribute AS shopAttribute'])
            ->where(['avpml.mod_id' => $this->mod_id])
            ->groupBy('avpml.attribute_value_id')
            ->orderBy('shopAttribute.sort asc')
            ->addOrderBy('attributeValue.sort asc')
            ->asArray()
            ->all();
        $attribute = [];
        foreach ($avpml as $a) {
            $attribute[$a['attributeValue']['shopAttribute']['slug']] = [
                'id' => $a['attributeValue']['shopAttribute']['id'],
                'name' => $a['attributeValue']['shopAttribute']['name'],
                'sort' => $a['attributeValue']['shopAttribute']['sort'],
                'slug' => $a['attributeValue']['shopAttribute']['slug']
            ];
        }
        if (isset($this->searchModel->queryGET['attribute_mod']) && $this->searchModel->queryGET['attribute_mod']) {
            foreach ($this->searchModel->queryGET['attribute_mod'] as $attribute_mod) {
                $this->checked = array_merge($this->checked, $attribute_mod);
            }
        }
        return $this->render('filter', [
            'attribute' => $attribute,
            'avpml' => $avpml,
            'searchModel' => $this->searchModel,
            'priceMaxMin' => $this->priceMaxMin,
            'filter' => $this
        ]);
    }

    public function checkedMod($id)
    {
        if (in_array($id, $this->checked)) {
            return 'checked';
        }
    }
}
