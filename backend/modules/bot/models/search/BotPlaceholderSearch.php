<?php

namespace backend\modules\bot\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\bot\models\BotPlaceholder;


class BotPlaceholderSearch extends BotPlaceholder
{
    public $query;

    public function rules()
    {
        return [
            [['id', 'sort', 'status'], 'integer'],
            [['slug', 'name', 'text', 'text_example'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $this->query = BotPlaceholder::find()->orderBy('sort asc');

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $this->query->andFilterWhere([
            'id' => $this->id,
            'sort' => $this->sort,
            'status' => $this->status,
        ]);

        $this->query->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'text_example', $this->text_example]);

        return $dataProvider;
    }
}
