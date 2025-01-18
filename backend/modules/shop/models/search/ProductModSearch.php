<?php

namespace backend\modules\shop\models\search;

use backend\modules\shop\models\Category;
use backend\modules\shop\models\CustomerFilter;
use backend\modules\shop\models\Product;
use backend\modules\shop\models\ProductMod;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ProductModSearch extends ProductMod
{
    public $query;
    public $queryGET = [];

    public function rules()
    {
        return [
            [['name', 'slug', 'price', 'name', 'code', 'sort', 'attribute_mod'], 'safe'],
            [['name', 'slug', 'name', 'code'], 'trim'],
            [['id', 'category_id', 'product_id', 'price_from', 'price_to', 'status'], 'integer'],
            ['price_to', 'compare', 'compareAttribute' => 'price_from', 'operator' => '>=', 'type' => 'number', 'message' => 'Значение "До" должно быть больше значения "От"'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return ArrayHelper::merge(parent::attributes(), ['price_from', 'price_to', 'discount_id', 'category_id', 'q', 'attribute_mod', 'code']);
    }

    public function search($params)
    {
        $this->query = ProductMod::find()
            ->alias('mod')
            ->joinWith([
                'product AS product',
                'product.category AS category'
            ])
            ->rightJoin('shop_product as p', 'mod.product_id = p.id')
            ->andWhere(['>=', 'p.qty_total', 1]);

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'sort' => [
                'enableMultiSort' => true,
                'defaultOrder' => ['price' => SORT_ASC],
                'attributes' => [
                    'id' => [
                        'asc' => ['mod.id' => SORT_ASC],
                        'desc' => ['mod.id' => SORT_DESC],
                    ],
                    'name' => [
                        'asc' => ['mod.name' => SORT_ASC],
                        'desc' => ['mod.name' => SORT_DESC],
                    ],
                    'discount_id' => [
                        'asc' => ['product.discount_id' => SORT_ASC],
                        'desc' => ['product.discount_id' => SORT_DESC],
                    ],
                    'status' => [
                        'asc' => ['mod.status' => SORT_ASC],
                        'desc' => ['mod.status' => SORT_DESC],
                    ],
                    'category_id' => [
                        'asc' => ['product.category_id' => SORT_ASC],
                        'desc' => ['product.category_id' => SORT_DESC],
                    ],
                    'rating' => [
                        'asc' => ['product.rating' => SORT_ASC],
                        'desc' => ['product.rating' => SORT_DESC],
                    ],
                    'price' => [
                        'asc' => ['mod.price' => SORT_ASC],
                        'desc' => ['mod.price' => SORT_DESC],
                    ],

                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if ($this->attribute_mod) {
            $Ids = $this->filter($this->attribute_mod);
            if ($Ids !== null) {
                $this->query->andWhere(['mod.id' => $Ids]);
            }
        }
        $this->query->andFilterWhere([
            'mod.id' => $this->id,
            'product.category_id' => $this->category_id,
            'product.discount_id' => $this->discount_id,
            'mod.status' => $this->status,
            'mod.status' => $this->status,
        ]);

        $this->from_to('price_from', 'price_to', 'mod.price');

        $this->query->andFilterWhere(['like', 'mod.name', $this->name])
            ->andFilterWhere(['like', 'mod.slug', $this->slug]);
        return $dataProvider;
    }

    private function filter($attribute_mod)
    {
        $Ids = null;
        foreach ($attribute_mod as $item) {
            $a = implode(',', $item);
            $data = Yii::$app->db->createCommand("SELECT mod_id FROM shop_attribute_value_product_mod_link WHERE attribute_value_id IN ($a)")->queryAll();
            $foundId = ArrayHelper::getColumn($data, 'mod_id');
            $Ids = $Ids === null ? $foundId : array_intersect($Ids, $foundId);
        }
        return $Ids;
    }

    public function parsingAddressBar()
    {
        $get = Yii::$app->request->get();
        if (isset($get['slug'])) {
            unset($get['slug']);
        }
        if (isset($get['sort'])) {
            unset($get['sort']);
        }
        if (isset($get['page'])) {
            unset($get['page']);
        }
        if (isset($get['per-page'])) {
            unset($get['per-page']);
        }

        if (isset($get['price_from'])) {
            $this->queryGET = $this->queryGET + ['price_from' => $get['price_from']];
            unset($get['price_from']);
        }
        if (isset($get['price_to'])) {
            $this->queryGET = $this->queryGET + ['price_to' => $get['price_to']];
            unset($get['price_to']);
        }
        $attribute_mod = [];
        if ($get) {
            foreach ($get as $item) {
                $attribute_mod[] = explode(',', $item);
            }
        }

        $this->queryGET = $this->queryGET + ['attribute_mod' => $attribute_mod];
        return [
            'ProductModSearch' => $this->queryGET,
            'sort' => Yii::$app->request->get('sort'),
            'page' => Yii::$app->request->get('page'),
            'per-page' => Yii::$app->request->get('per-page')
        ];
    }

    private function from_to($from, $to, $field)
    {
        if (!empty($this->{$from}) && !empty($this->{$to})) {
            return $this->query->andFilterWhere(['between', $field, $this->{$from}, $this->{$to}]);
        }
        if (!empty($this->{$from})) {
            return $this->query->andFilterWhere(['>=', $field, $this->{$from}]);
        }
        if (!empty($this->{$to})) {
            return $this->query->andFilterWhere(['<=', $field, $this->{$to}]);
        }
    }

    public function checkExistFilter()
    {
        if (isset($this->queryGET['price_from'])) {
            return false;
        }
        if (isset($this->queryGET['price_to'])) {
            return false;
        }
        if ($this->queryGET['attribute_mod']) {
            return false;
        }
        return true;
    }

    public static function searchBot($attributeValueId)
    {
        $searchModel = new self();
        $dataProvider = $searchModel->search(['ProductModSearch' => ['attribute_mod' => $attributeValueId]]);
        $searchModel->query->select('mod.id');
        $searchModel->query->andFilterWhere([
            'product.status' => Product::STATUS_ACTIVE,
            'mod.status' => ProductMod::STATUS_ACTIVE,
            'category.status' => Category::STATUS_ACTIVE
        ]);
        $dataProvider->setPagination(['pageSize' => false]);
        return $dataProvider;
    }
}



