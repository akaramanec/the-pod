<?php

namespace backend\modules\media\models;

class FileInit extends ImgInit
{
    public function saveMain($id, $name_file)
    {
        $this->setEntityImg();
        $this->_entityImg::updateAll(['video' => $name_file], ['=', 'id', $id]);
    }

    public function deleteMainFile($id)
    {
        $this->setEntityImg();
        $model = $this->_entityImg::findOne($id);
        @unlink($this->path . $id . DIRECTORY_SEPARATOR . $model->video);
        $model->video = null;
        $model->save(false);
    }
}
