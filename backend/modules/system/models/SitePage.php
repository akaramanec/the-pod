<?php

namespace backend\modules\system\models;

use backend\modules\media\models\ImgSave;
use Yii;
use yii\web\NotFoundHttpException;

class SitePage extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;

    public static function tableName()
    {
        return 'site_page';
    }
    public function behaviors()
    {
        return [
            [
                'class' => ImgSave::className(),
                'entityImg' => SITE_PAGE
            ],
        ];
    }
    public function rules()
    {
        return [
            [['img'], 'string', 'max' => 20],
            [['status'], 'integer'],
            [['bg'], 'string', 'max' => 7, 'min' => 7],
            [['slug'], 'unique'],
            [['mainImg'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, jpeg'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'img' => 'Main Img',
            'bg' => 'Цвет',
            'slug' => 'Url',
        ];
    }

    public function getLang()
    {
        return $this->hasOne(SitePageL::className(), ['page_id' => 'id'])->andWhere(['lang' => Yii::$app->language]);
    }

    public function getLangUk()
    {
        return $this->hasOne(SitePageL::className(), ['page_id' => 'id'])->andWhere(['lang' => 'uk']);
    }

    public function getLangRu()
    {
        return $this->hasOne(SitePageL::className(), ['page_id' => 'id'])->andWhere(['lang' => 'ru']);
    }

    public static function page($slug)
    {
        if (($page = self::find()->where(['slug' => $slug])->with(['lang'])->limit(1)->one()) !== null) {
            return $page;
        }
        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }

    public static function statusesAll()
    {
        return [
            self::STATUS_INACTIVE => 'Не активен',
            self::STATUS_ACTIVE => 'Активен',
        ];
    }


}
