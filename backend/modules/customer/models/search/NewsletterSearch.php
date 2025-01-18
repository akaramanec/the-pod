<?php

namespace backend\modules\customer\models\search;

use backend\modules\customer\models\Newsletter;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class NewsletterSearch extends Newsletter
{
    public $query;

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['text', 'setting'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $this->query = Newsletter::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'sort' => [
                'defaultOrder' => ['date_departure' => SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $this->query->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'setting', $this->setting]);

        return $dataProvider;
    }
}
