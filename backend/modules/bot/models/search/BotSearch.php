<?php

namespace backend\modules\bot\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\bot\models\Bot;


class BotSearch extends Bot
{

    public function rules()
    {
        return [
            [['id','status'], 'integer'],
            [['platform', 'username', 'first_name', 'key'], 'safe'],
        ];
    }


    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Bot::find();


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

        $query->andFilterWhere(['like', 'platform', $this->platform])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'first_name', $this->first_name]);

        return $dataProvider;
    }
}
