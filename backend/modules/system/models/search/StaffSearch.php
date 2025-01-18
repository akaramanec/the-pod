<?php

namespace backend\modules\system\models\search;

use backend\modules\system\models\Staff;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StaffSearch extends Staff
{

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'description', 'img'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Staff::find()->orderBy('sort asc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'img', $this->img]);

        return $dataProvider;
    }
}
