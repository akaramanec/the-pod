<?php

namespace backend\modules\shop\models\search;

use backend\modules\shop\models\Product;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['status', 'category_id'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]],
            'pagination' => [
                'defaultPageSize' => '30',
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}