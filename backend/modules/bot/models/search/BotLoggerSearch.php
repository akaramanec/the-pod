<?php

namespace backend\modules\bot\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\bot\models\BotLogger;

class BotLoggerSearch extends BotLogger
{

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['data', 'created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = BotLogger::find();


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
