<?php

namespace backend\modules\system\models;

use src\behavior\Addition;
use src\behavior\CapitalLetters;
use src\behavior\JsonMetaStatic;
use Yii;

/**
 *
 * @property-read mixed $page
 * @property int $id [int(10) unsigned]
 * @property int $page_id [int(10) unsigned]
 * @property string $lang [enum('en', 'uk', 'ru')]
 * @property string $name [varchar(255)]
 * @property string $description
 * @property string $meta [json]
 * @property string $content
 * @property array $addition
 */
class SitePageL extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'site_page_l';
    }

    public function behaviors()
    {
        return [
            [
                'class' => JsonMetaStatic::className(),
            ],
            [
                'class' => Addition::className(),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['page_id', 'name'], 'required'],
            [['lang', 'description', 'content', 'meta_title', 'meta_description'], 'string'],
            [['page_id'], 'integer'],
            [['meta', 'addition'], 'safe'],
//            ['meta', 'default', 'value' => '{"title": "title", "keywords": "keywords", "description": "description"}'],
            [['name'], 'string', 'max' => 255],
            [['name', 'description', 'meta_title', 'meta_description'], 'trim'],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => SitePage::className(), 'targetAttribute' => ['page_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'page_id' => 'Page ID',
            'lang' => 'Язык',
            'name' => 'Название',
            'description' => 'Описание',
            'content' => 'Контент',
            'meta' => 'Meta',
        ];
    }

    public function getPage()
    {
        return $this->hasOne(SitePage::className(), ['id' => 'page_id']);
    }
}
