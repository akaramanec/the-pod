<?php

namespace backend\modules\bot\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\bot\models\BotCommand;

class BotCommandSearch extends BotCommand
{
    public $query;

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'description'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $this->query = BotCommand::find()
            ->alias('command')
            ->orderBy('command.id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
        ]);

        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;
        }

        $this->query->andFilterWhere([
            'command.id' => $this->id,
            'command.status' => $this->status,
        ]);

        $this->query->andFilterWhere(['like', 'command.name', $this->name])
            ->andFilterWhere(['like', 'command.description', $this->description]);

        return $dataProvider;
    }
}
