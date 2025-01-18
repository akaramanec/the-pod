<?php

namespace backend\modules\shop\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\shop\models\Faq;


class FaqSearch extends Faq
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
        $this->query = Faq::find();


        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'sort' => [
                'enableMultiSort' => true,
                'defaultOrder' => ['sort' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        $this->query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
