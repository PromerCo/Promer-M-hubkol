<?php
namespace mhubkol\services;

use mhubkol\models\HubkolTags;

class HubkolTagsService extends HubkolTags{

    /*
   * 找到平台对应的 标签
   */
    public function  actionLabel($platform_id=998){
        $data =   HubkolTags::find()->where(['platform_id'=>$platform_id])->select([''])->asarray()->all();
        return $data;
    }
}
