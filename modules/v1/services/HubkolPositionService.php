<?php
namespace mhubkol\modules\v1\services;

use mhubkol\modules\v1\models\HubkolPosition;

class HubkolPositionService extends HubkolPosition {
    /*
     * 职位的三级分类
    */
    public function actionPosition(){
        $data =   HubkolPosition::find()->select('id,code,name,parent_code')->asarray()->all();
        $result =  Common::getTree($data,'100000',0);
        return  $result;
    }

}
