<?php

namespace backend\modules\media\models;

use backend\modules\admin\models\AuthAdmin;
use backend\modules\customer\models\Newsletter;
use backend\modules\shop\models\Faq;
use backend\modules\shop\models\Notice;
use backend\modules\shop\models\NoticeNp;
use backend\modules\system\models\ReviewSlider;
use backend\modules\system\models\SitePage;
use backend\modules\system\models\Staff;
use Yii;
use yii\base\BaseObject;

class ImgInit extends BaseObject
{
    public $entity;
    public $relativePathCache;
    public $relativePath;
    public $relativePathUrl;
    public $path;
    public $pathCache;
    protected $_entityImg;

    public function init()
    {
        $this->path();
        parent::init();
    }

    public function path()
    {
        switch ($this->entity) {
            case SHOP_CATEGORY:
                $dir = 'category';
                break;
            case SHOP_PRODUCT:
                $dir = 'product';
                break;
            case NEWSLETTER:
                $dir = 'newsletter';
                break;
            case ADMIN:
                $dir = 'admin';
                break;
            case NOTICE:
                $dir = 'notice';
                break;
            case NOTICE_NP:
                $dir = 'notice_np';
                break;
            case POD_FAQ:
                $dir = 'faq';
                break;
            case SITE_PAGE:
                $dir = 'site_page';
                break;
            case REVIEW_SLIDER:
                $dir = 'review_slider';
                break;
            case STAFF:
                $dir = 'staff';
                break;
            default:
                throw new \Exception('no entity');
        }
        $this->relativePathCache = '/cache/' . $dir . '/';
        $this->relativePath = '/' . $dir . '/';
        $this->relativePathUrl = Yii::$app->params['imgUrl'] . $this->relativePath;
        $this->path = Yii::$app->params['imgPath'] . '/' . $dir . '/';
        $this->pathCache = Yii::$app->params['imgPath'] . '/cache/' . $dir . '/';
    }

    public function setEntityImg()
    {
        switch ($this->entity) {
            case POD_FAQ:
                $this->_entityImg = new Faq();
                break;
            case SHOP_CATEGORY:
                $this->_entityImg = new Category();
                break;
            case SHOP_PRODUCT:
                $this->_entityImg = new Product();
                break;
            case NEWSLETTER:
                $this->_entityImg = new Newsletter();
                break;
            case ADMIN:
                $this->_entityImg = new AuthAdmin();
                break;
            case NOTICE:
                $this->_entityImg = new Notice();
                break;
            case NOTICE_NP:
                $this->_entityImg = new NoticeNp();
                break;
            case SITE_PAGE:
                $this->_entityImg = new SitePage();
                break;
            case REVIEW_SLIDER:
                $this->_entityImg = new ReviewSlider();
                break;
            case STAFF:
                $this->_entityImg = new Staff();
                break;
            default:
                throw new \Exception('ImgInit setEntityImg');
        }
    }

    public function saveMainImg($id, $name_img)
    {
        $this->setEntityImg();
        $this->_entityImg::updateAll(['img' => $name_img], ['=', 'id', $id]);
    }

    public function deleteMainImg($id)
    {
        $this->setEntityImg();
        $model = $this->_entityImg::findOne($id);
        @unlink($this->path . $id . DIRECTORY_SEPARATOR . $model->img);
        $model->img = null;
        $model->save(false);
    }

    public function getEntityImg()
    {
        return $this->_entityImg;
    }
}
