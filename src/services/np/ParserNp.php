<?php


namespace src\services\np;


use backend\modules\bot\models\BotLogger;
use backend\modules\bot\src\AddressNp;
use backend\modules\bot\src\ApiNp;
use backend\modules\system\models\NpArea;


class ParserNp
{
    public $apiNp;
    public $addressNp;

    public function __construct()
    {
        $this->apiNp = new ApiNp();
        $this->addressNp = new AddressNp($this->apiNp);
//        $this->saveAreas();
    }


    public function saveAreas()
    {
        foreach ($this->addressNp->getAreas()->data as $item) {
            $npArea = new NpArea();
            $npArea->Ref = $item->Ref;
            $npArea->AreasCenter = $item->AreasCenter;
            $npArea->DescriptionRu = $item->DescriptionRu;
            $npArea->Description = $item->Description;;
            if (!$npArea->save()) {
                BotLogger::save_input([$npArea->errors]);
            }
        }
    }
}
