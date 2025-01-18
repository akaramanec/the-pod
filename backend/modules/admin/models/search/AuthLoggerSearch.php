<?php

namespace backend\modules\admin\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\admin\models\AuthLogger;

class AuthLoggerSearch extends AuthLogger
{

    public function rules()
    {
        return [
            [['id', 'admin_id'], 'integer'],
            [['controller', 'action', 'request', 'created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = AuthLogger::find()->orderBy('id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'admin_id' => $this->admin_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'controller', $this->controller])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'request', $this->request]);

        return $dataProvider;
    }
}
