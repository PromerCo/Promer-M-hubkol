<?php
namespace mhubkol\modules\v1\controllers;

use mhubkol\common\helps\Common;
use mhubkol\common\helps\HttpCode;
use mhubkol\modules\v1\models\HubkolPush;
use mhubkol\modules\v1\models\HubkolTags;
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

         $start_page = \Yii::$app->request->post('start_page')??0;
         $end_page   = \Yii::$app->request->post('end_page')??5;

         $data =  HubkolPush::findBySql("SELECT hubkol_push.id,hubkol_push.tags,hubkol_hub.brand,hubkol_push.enroll,hubkol_push.title as push_title,
hubkol_push.convene,hubkol_push.bystander_number,hubkol_push.create_date,hubkol_follow.title,hubkol_user.avatar_url,hubkol_platform.logo,
hubkol_platform.id as platform_id FROM hubkol_push  LEFT JOIN hubkol_follow ON hubkol_push.follow_level = hubkol_follow.id
LEFT JOIN hubkol_platform ON hubkol_push.platform = hubkol_platform.id
LEFT JOIN hubkol_hub ON hubkol_push.hub_id = hubkol_hub.id
LEFT JOIN hubkol_user ON hubkol_user.id = hubkol_hub.uid
WHERE hubkol_push.expire_time > NOW()
GROUP BY hubkol_push.id ORDER by hubkol_push.create_date DESC LIMIT $start_page,$end_page")->asArray()->all();

  foreach ($data as $key=>$value){
      $data[$key]['tages'] =   HubkolTags::findBySql("SELECT title,id FROM hubkol_tags WHERE id in (".$value['tags'].")")->asArray()->all();
      $data[$key]['create_time'] = Common::time_tranx($value['create_date'],1);
  }
  return  HttpCode::renderJSON($data,'ok','200');
    }

    /*
     * 详情页
     */
    public function actionDetails(){
        $phsh_id = \Yii::$app->request->post('push_id');
        $data = HubkolPush::findBySql("SELECT hubkol_push.id,hubkol_hub.city,hubkol_push.title as push_title,hubkol_platform.title,
        hubkol_platform.logo,hubkol_push.expire_time,hubkol_push.create_date,hubkol_hub.brand,hubkol_user.avatar_url,hubkol_user.nick_name,
        hubkol_follow.title,hubkol_push.enroll,hubkol_push.describe,hubkol_push.bystander_number,hubkol_push.tags,hubkol_pull.is_collect,
        hubkol_pull.is_enroll,hubkol_push.convene,hubkol_push.enroll_number 
        FROM hubkol_push  
        LEFT JOIN hubkol_platform ON hubkol_push.platform = hubkol_platform.id
        LEFT JOIN hubkol_hub ON  hubkol_push.hub_id = hubkol_hub.id 
        LEFT JOIN hubkol_user ON hubkol_user.id = hubkol_hub.uid
        LEFT JOIN hubkol_follow ON hubkol_follow.id = hubkol_push.follow_level
        LEFT JOIN hubkol_pull   ON hubkol_pull.push_id = hubkol_push.id
        WHERE  hubkol_push.id = $phsh_id")->asArray()->one();

        //time_tranx
        $data['expire_time']  = Common::time_tranx($data['expire_time']);
        $data['create_time'] = Common::time_tranx($data['create_date'],1);

        $data['tages'] =   HubkolTags::findBySql("SELECT title,id FROM hubkol_tags WHERE id in (".$data['tags'].")")->asArray()->all();
        return  HttpCode::renderJSON($data,'ok','200');
    }



}
