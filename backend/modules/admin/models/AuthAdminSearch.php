<?php

namespace backend\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class AuthAdminSearch extends AuthAdmin
{

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['surname', 'name', 'phone', 'email', 'auth_key', 'created_at', 'position', 'company'], 'trim'],
            [['surname', 'name', 'phone', 'email', 'auth_key', 'created_at', 'position', 'company'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return ArrayHelper::merge(parent::attributes(), ['position', 'company']);
    }

    public function search($params)
    {
        $query = AuthAdmin::find();

        if (Yii::$app->controller->action->id == 'index') {
            $query->joinWith(['roles AS admin']);
        }

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
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'surname', $this->surname])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'customer.position', $this->position])
            ->andFilterWhere(['like', 'customer.company', $this->company])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key]);

        return $dataProvider;
    }
}
