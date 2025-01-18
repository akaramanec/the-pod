<?php

namespace backend\modules\shop\models\search;

use backend\modules\shop\models\NoticeNp;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class NoticeNpSearch extends NoticeNp
{
    public $query;

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
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
        $this->query = NoticeNp::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $this->query->andFilterWhere([
            'id' => $this->id,
        ]);

        $this->query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
