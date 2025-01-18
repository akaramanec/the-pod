<?php

namespace backend\modules\bot\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\bot\models\BotMenu;

class BotMenuSearch extends BotMenu
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'slug', 'command_id'], 'safe'],
            [['name', 'slug', 'command_id'], 'trim'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = BotMenu::find()
            ->alias('botMenu')
            ->joinWith(['command AS command'])->orderBy('id desc');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'botMenu.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'botMenu.name', $this->name])
            ->andFilterWhere(['like', 'command.name', $this->command_id])
            ->andFilterWhere(['like', 'slug', $this->slug]);

        return $dataProvider;
    }
}
