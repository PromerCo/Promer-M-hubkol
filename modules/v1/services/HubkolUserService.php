<?php
namespace mhubkol\modules\v1\services;

use mhubkol\common\helps\HttpCode;
use mhubkol\modules\v1\models\HubkolHub;
use mhubkol\modules\v1\models\HubkolKol;
use mhubkol\modules\v1\models\HubkolTags;
use mhubkol\modules\v1\models\HubkolUser;

class HubkolUserService extends HubkolUser {

    public static function Blocked($type,$uid){



        switch ($type){


            //HUB
            case 1:
                $hub  =  HubkolHub::find()->where(['uid'=>$uid])->select(['wechat','phone','email',
                    'industry','company','brand','position_code','city','profile','province','province_code','city_code'])->asArray()->one();

                if ($hub){
                    $hub['status'] = 1;
                    $hub['type'] = 1;
                    return  HttpCode::renderJSON($hub,'ok','201');
                }else{
                    $hub['status'] = 0;
                    $hub['type'] = 1;
                    return  HttpCode::renderJSON($hub,'null','201');
                }

                break;
            //KOL
            case 2:
                $hub = HubkolKol::findBySql("SELECT hubkol_kol.wechat,hubkol_kol.email,hubkol_kol.mcn_organization,hubkol_kol.mcn_company,hubkol_kol.phone,
hubkol_kol.city,hubkol_platform.title,hubkol_kol.platform,hubkol_kol.tags,hubkol_kol.account,hubkol_kol.follow_level,hubkol_kol.province_code,
hubkol_kol.city_code,hubkol_kol.province,hubkol_follow.title as fs_title FROM hubkol_kol LEFT JOIN hubkol_platform  ON hubkol_kol.platform = hubkol_platform.id
LEFT JOIN hubkol_follow ON hubkol_follow.id = hubkol_kol.follow_level
WHERE hubkol_kol.uid = $uid")->asArray()->one();

                if ($hub){
                    $hub['tags'] =   HubkolTags::findBySql("SELECT title,id FROM hubkol_tags WHERE id in (".$hub['tags'].")")->asArray()->all();
                    $hub['status'] = 1;
                    $hub['type'] = 2;
                    return  HttpCode::renderJSON($hub,'ok','201');
                }else{
                    //新增一条数据
                    $hub['status'] = 0;
                    $hub['type'] = 2;
                    return  HttpCode::renderJSON($hub,'null','201');
                }
                break;
        }
    }

}
