<?php
namespace mhubkol\modules\v1\controllers;

use mhubkol\common\helps\HttpCode;
use mhubkol\modules\v1\models\HubkolKol;
use mhubkol\modules\v1\models\HubkolPlatform;
use mhubkol\modules\v1\models\HubkolPull;
use mhubkol\modules\v1\models\HubkolPush;
use mhubkol\modules\v1\models\HubkolTags;
use mhubkol\modules\v1\models\HubkolUser;
use mhubkol\modules\v1\services\ParamsValidateService;

use mhubkol\common\components\Redis;

/**
 * Site controller
 */
class KolController extends BaseController
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
     * KOL 列表
     */
    public function actionList(){
        $Hubs = HubkolPlatform::find()->with('retion')->select(['title','logo','id'])->asArray()->all();
        foreach ($Hubs as $key =>$value){
            foreach ($value['retion'] as $k =>$v){
                $Hubs[$key]['retion'][$k]   = HubkolTags::find()->where(['id'=>$v['tags_id']])->select(['title','id'])->asArray()->one();
            }
        }
        $list =  Redis::get('list');
        Redis::del('list');
        if (!$list){
           $list = $Hubs;
           Redis::set('list',$Hubs);
        }
        return  HttpCode::jsonObj($list,'ok','201');
    }
    /*
     * 数据展示
     */
    public function  actionSpread(){
        $data = \Yii::$app->request->post();

        $start_page = $data['start_page']??0;

        if (empty($data['type']) || $data['type']==0){
            $result = HubkolKol::findBySql("SELECT hubkol_user.avatar_url,hubkol_kol.tags,hubkol_kol.id,
hubkol_user.nick_name,hubkol_follow.title,hubkol_kol.mcn_organization,hubkol_kol.city,
hubkol_platform.logo,hubkol_platform.id as platform_id  FROM  hubkol_kol
LEFT JOIN hubkol_user ON hubkol_kol.uid = hubkol_user.id
LEFT JOIN  hubkol_follow ON  hubkol_follow.id = hubkol_kol.follow_level
LEFT JOIN hubkol_platform ON hubkol_platform.id = hubkol_kol.platform LIMIT $start_page,5")->asArray()->all();
        }else{
            $pvs = new ParamsValidateService();
            $valid = $pvs->validate($data, [
                [['platform_id'], 'required']
            ]);
            if (!$valid){
                return  HttpCode::jsonObj([],$pvs->getErrorSummary(true),'416');
            }
            $platform_id = $data['platform_id'];

            $result = HubkolKol::findBySql("SELECT hubkol_user.avatar_url,
hubkol_user.nick_name,hubkol_follow.title,hubkol_kol.mcn_organization,hubkol_kol.city,hubkol_kol.tags,hubkol_kol.id,
hubkol_platform.logo,hubkol_platform.id as platform_id  FROM  hubkol_kol
LEFT JOIN hubkol_user ON hubkol_kol.uid = hubkol_user.id
LEFT JOIN  hubkol_follow ON  hubkol_follow.id = hubkol_kol.follow_level
LEFT JOIN hubkol_platform ON hubkol_platform.id = hubkol_kol.platform where  hubkol_kol.platform = $platform_id LIMIT $start_page,5")->asArray()->all();
        }
        foreach ($result as $key=>$value){
            $result[$key]['tages'] =   HubkolTags::findBySql("SELECT title,id FROM hubkol_tags WHERE id in (".$value['tags'].")")->asArray()->all();
        }
        return  HttpCode::jsonObj($result,'ok','201');
    }

    /*
     * 我报名(发布)的栏目
     */
    public function actionLame(){
        $uid =   $this->uid;   //获取用户ID
        //查看用户角色
        $capacity =   HubkolUser::find()->where(['id'=>$uid])->select(['capacity'])->asArray()->one();
        switch ($capacity['capacity']){
            case 0:
                return  HttpCode::renderJSON([],'资料不存在','416');
            break;
            case 1:
                $data =    HubkolPush::findBySql("SELECT  hubkol_push.id,hubkol_push.title,hubkol_platform.logo,hubkol_push.create_date FROM  hubkol_push
LEFT JOIN  hubkol_hub ON hubkol_push.hub_id = hubkol_hub.id
LEFT JOIN  hubkol_platform ON hubkol_platform.id = hubkol_push.platform
WHERE hubkol_hub.uid =$uid  ORDER BY hubkol_push.create_date desc")->asArray()->all();
                return  HttpCode::renderJSON($data,'ok','201');
            break;
            case 2:
            $data =    HubkolPull::findBySql("SELECT  hubkol_push.id,hubkol_push.title,hubkol_platform.logo,hubkol_pull.is_enroll,hubkol_push.create_date FROM  hubkol_pull 
LEFT JOIN  hubkol_kol ON hubkol_pull.kol_id = hubkol_kol.id
LEFT JOIN hubkol_push ON hubkol_pull.push_id = hubkol_push.id
LEFT JOIN  hubkol_platform ON hubkol_platform.id = hubkol_push.platform
WHERE hubkol_kol.uid =$uid  AND hubkol_pull.is_enroll = 1
ORDER BY hubkol_push.create_date desc")->asArray()->all();
                return  HttpCode::renderJSON($data,'ok','201');
            break;
        }
    }

    /*
     * KOL (网红) 详情
     */
    public function actionKolpro(){
      $pro_id =  \Yii::$app->request->post('pro_id');
      if (empty($pro_id)){
          return  HttpCode::jsonObj([],'参数不能为空','412');
      }
      $data   =  HubkolKol::findBySql("SELECT hubkol_user.avatar_url,hubkol_kol.city,hubkol_kol.mcn_organization,hubkol_kol.tags,
hubkol_user.nick_name,hubkol_follow.title,hubkol_kol.`profile` FROM hubkol_kol 
LEFT JOIN hubkol_user ON hubkol_user.id = hubkol_kol.uid
LEFT JOIN hubkol_follow ON hubkol_kol.follow_level = hubkol_follow.id
WHERE hubkol_kol.id = $pro_id")->asArray()->all();

      foreach ($data as $key => $value){

              $data[$key]['tages'] =   HubkolTags::findBySql("SELECT title,id FROM hubkol_tags WHERE id in (".$value['tags'].")")->asArray()->all();

      }
        return  HttpCode::jsonObj($data,'ok','201');

    }

   public function assoc_unique($arr, $key)
    {
        $tmp_arr = array();
        foreach($arr as $k => $v)
        {
            if(in_array($v[$key], $tmp_arr))//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
            {
                unset($arr[$k]);
            }
            else {
                $tmp_arr[] = $v[$key];
            }
        }
        sort($arr); //sort函数对数组进行排序
        return $arr;
    }
}
