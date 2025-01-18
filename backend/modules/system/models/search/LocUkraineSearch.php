<?php

namespace backend\modules\system\models\search;

use backend\modules\system\models\LocUkraine;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class LocUkraineSearch extends LocUkraine
{

    public function rules()
    {
        return [
            [['ukraine_id', 'parent_id', 'code', 'level'], 'integer'],
            [['ukraine_id', 'parent_id', 'code', 'level'], 'trim'],
            [['name', 'new_name', 'type'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = LocUkraine::find();


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ukraine_id' => $this->ukraine_id,
            'parent_id' => $this->parent_id,
            'code' => $this->code,
            'level' => $this->level,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'new_name', $this->new_name])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
