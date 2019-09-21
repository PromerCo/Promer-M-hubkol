<?php
namespace apiminip\services;
use apiminip\common\helps\HttpCode;
use apiminip\models\HubkolHub;
use apiminip\models\HubkolKol;
use apiminip\models\WechatUser;

class WechatUserService extends WechatUser{

    public static function Blocked($type,$uid){
        switch ($type){
            //未绑定资料
            case 0:
                return  HttpCode::renderJSON([],'请先绑定资料','204');
            //HUB
            case 1:

                $hub  =  HubkolHub::find()->where(['uid'=>$uid])->select(['wechat','phone','email',
                    'industry','company','brand','position_code','city','profile','province','province_code','city_code'])->asArray()->one();

                $hub['type'] = 1;
                return  HttpCode::renderJSON($hub,'ok','201');
                break;
            //KOL
            case 2:
                $hub  =  HubkolKol::find()->where(['uid'=>$uid])->select(['wechat','phone','email',
                    'mcn_organization','mcn_company','city','platform','tags','account','follow_level','profile','province_code','city_code','province'])->asArray()->one();
                $hub['type'] = 2;
                return  HttpCode::renderJSON($hub,'ok','201');
                break;
        }
    }

}
