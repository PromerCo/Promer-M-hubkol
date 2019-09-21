<?php
namespace apiminip\modules\v1\controllers;
use apiminip\common\helps\Common;
use apiminip\common\helps\HttpCode;
use apiminip\models\HubkolPush;
use apiminip\models\HubkolTags;
use yii\web\Controller;

/**
 * Site controller
 */
class HomeController extends Controller
{
    public  $enableCsrfValidation=false;
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /*
     * 首页数据
     */
    public function  actionIndex(){

  $data =  HubkolPush::findBySql("SELECT hubkol_push.id,hubkol_pull.is_enroll,hubkol_push.tags,hubkol_hub.brand,hubkol_hub.city,hubkol_hub.province,hubkol_hub.industry,hubkol_push.enroll,hubkol_push.title as push_title,hubkol_push.budget,hubkol_push.`describe`,hubkol_push.type,
hubkol_push.convene,hubkol_push.bystander_number,hubkol_push.enroll_number,hubkol_push.expire_time,hubkol_push.create_date,
hubkol_follow.title,hubkol_hub.wechat,hubkol_hub.phone,hubkol_hub.email,wechat_user.nick_name,wechat_user.avatar_url,
hubkol_platform.logo,hubkol_platform.id as platform_id,hubkol_platform.title as platform_title
FROM hubkol_push  LEFT JOIN hubkol_follow ON hubkol_push.follow_level = hubkol_follow.id  
LEFT JOIN hubkol_platform ON hubkol_push.platform = hubkol_platform.id 
LEFT JOIN hubkol_hub ON hubkol_push.hub_id = hubkol_hub.id  
LEFT JOIN wechat_user ON wechat_user.id = hubkol_hub.uid  
LEFT JOIN hubkol_pull ON hubkol_pull.push_id = hubkol_push.id
GROUP BY hubkol_push.id ORDER by hubkol_push.create_date DESC LIMIT 0,8")->asArray()->all();
  foreach ($data as $key=>$value){
      $data[$key]['tages'] =   HubkolTags::findBySql("SELECT title,id FROM hubkol_tags WHERE id in (".$value['tags'].")")->asArray()->all();
      $data[$key]['create_time'] = Common::time_tranx($value['create_date']);
  }
  return  HttpCode::renderJSON($data,'ok','200');
    }
}