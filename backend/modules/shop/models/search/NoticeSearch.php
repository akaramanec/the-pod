<?php

namespace backend\modules\shop\models\search;

use backend\modules\shop\models\Notice;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class NoticeSearch extends Notice
{

    public function rules()
    {
        return [
            [['id', 'idle_time', 'status'], 'integer'],
            [['name', 'text'], 'safe'],
            [['name', 'text'], 'trim'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Notice::find()->orderBy('idle_time asc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
