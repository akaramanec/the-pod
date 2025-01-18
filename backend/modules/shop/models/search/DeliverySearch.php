<?php

namespace backend\modules\shop\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\shop\models\Delivery;


class DeliverySearch extends Delivery
{
    public $query;

    public function rules()
    {
        return [
            [['id', 'status', 'sort'], 'integer'],
            [['name', 'description', 'slug'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $this->query = Delivery::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'sort' => $this->sort,
        ]);

        $this->query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'slug', $this->slug]);

        return $dataProvider;
    }
}
