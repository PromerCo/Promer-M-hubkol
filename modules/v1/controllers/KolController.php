<?php
namespace mhubkol\modules\v1\controllers;

use backend\models\WechatUser;
use mhubkol\common\components\RedisLock;
use mhubkol\common\helps\HttpCode;
use mhubkol\modules\v1\models\HubkolCarefor;
use mhubkol\modules\v1\models\HubkolHub;
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

        $platform_id = $data['platform_id'];

        if (empty($data['type']) || $data['type']==0){

            if ($platform_id == 0){
                $result = HubkolKol::findBySql("SELECT hubkol_user.avatar_url,hubkol_kol.tags,hubkol_kol.id,
hubkol_user.nick_name,hubkol_follow.title,hubkol_kol.mcn_organization,hubkol_kol.city,
hubkol_platform.logo,hubkol_platform.id as platform_id  FROM  hubkol_kol
LEFT JOIN hubkol_user ON hubkol_kol.uid = hubkol_user.id
LEFT JOIN  hubkol_follow ON  hubkol_follow.id = hubkol_kol.follow_level
LEFT JOIN hubkol_platform ON hubkol_platform.id = hubkol_kol.platform LIMIT $start_page,5")->asArray()->all();
            }else{
                $result = HubkolKol::findBySql("SELECT hubkol_user.avatar_url,hubkol_kol.tags,hubkol_kol.id,
hubkol_user.nick_name,hubkol_follow.title,hubkol_kol.mcn_organization,hubkol_kol.city,
hubkol_platform.logo,hubkol_platform.id as platform_id  FROM  hubkol_kol
LEFT JOIN hubkol_user ON hubkol_kol.uid = hubkol_user.id
LEFT JOIN  hubkol_follow ON  hubkol_follow.id = hubkol_kol.follow_level
LEFT JOIN hubkol_platform ON hubkol_platform.id = hubkol_kol.platform where  hubkol_kol.platform = $platform_id LIMIT $start_page,5")->asArray()->all();
            }

        }else{
            $pvs = new ParamsValidateService();
            $valid = $pvs->validate($data, [
                [['platform_id'], 'required']
            ]);
            if (!$valid){
                return  HttpCode::jsonObj([],$pvs->getErrorSummary(true),'416');
            }


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
WHERE hubkol_hub.uid =$uid AND hubkol_push.expire_time > NOW()
ORDER BY hubkol_push.create_date desc")->asArray()->all();
                return  HttpCode::renderJSON($data,'ok','201');
            break;
            case 2:
            $data =    HubkolPull::findBySql("SELECT  hubkol_push.id,hubkol_push.title,hubkol_platform.logo,hubkol_pull.is_enroll,hubkol_push.create_date FROM  hubkol_pull 
LEFT JOIN  hubkol_kol ON hubkol_pull.kol_id = hubkol_kol.id
LEFT JOIN hubkol_push ON hubkol_pull.push_id = hubkol_push.id
LEFT JOIN  hubkol_platform ON hubkol_platform.id = hubkol_push.platform
WHERE hubkol_kol.uid =$uid  AND hubkol_pull.is_enroll = 1
AND hubkol_push.expire_time > NOW()
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
      $data   =  HubkolKol::findBySql("SELECT hubkol_user.avatar_url,hubkol_kol.city,hubkol_kol.mcn_organization,hubkol_kol.tags,hubkol_kol.id,hubkol_kol.invite,hubkol_kol.invite_number,
hubkol_user.nick_name,hubkol_follow.title,hubkol_kol.`profile` FROM hubkol_kol 
LEFT JOIN hubkol_user ON hubkol_user.id = hubkol_kol.uid
LEFT JOIN hubkol_follow ON hubkol_kol.follow_level = hubkol_follow.id
WHERE hubkol_kol.id = $pro_id")->asArray()->one();
      //查看是否关注
        $capacity =   HubkolUser::find()->where(['id'=>$this->uid])->select(['capacity'])->asArray()->one()['capacity'];
        if ($capacity == 1){
            $hub_id = HubkolHub::find()->where(['uid'=>$this->uid])->select(['id'])->asArray()->one();
            if (!empty($hub_id['id'])){
            $follow =   HubkolCarefor::find()->where(['kol_id'=>$pro_id,'hub_id'=>$hub_id['id']])->select(['status'])->asArray()->one();
            if (empty($follow['status'])){
                $data['status'] = 0;
            }else{
                $data['status'] = $follow['status'];
            }
            }
        }else{
            $data['status'] = 0;
        }
       $data['tages'] =   HubkolTags::findBySql("SELECT title,id FROM hubkol_tags WHERE id in (".$data['tags'].")")->asArray()->all();
        return  HttpCode::renderJSON($data,'ok','201');
    }

    /*
    * 邀请KOL
    */
    public function actionInvite(){
        if ((\Yii::$app->request->isPost)) {
            $kol_id  = \Yii::$app->request->post('kol_id');
            $uid = $this->uid;
            $transaction = \Yii::$app->db->beginTransaction();
            if (empty($kol_id)){
                return  HttpCode::renderJSON([],'参数不能为空','406');
            }
            $key = 'mylock';//加锁
            $is_lock = RedisLock::lock($key);
            if ($is_lock){
              try{
                  /*
                   * 1.HUB身份才可以邀请
                   * 2.资料必须填写
                   * 3.该用户是否邀请过
                  */
                  //获取用户身份
                  $userinfo =   HubkolUser::find()->where(['id'=>$uid])->select(['capacity','avatar_url'])->asArray()->one();
                  //用户身份为HUB
                  if ($userinfo['capacity'] == 1){
                  //HUB用户是否填写资料
                  $hub_id = HubkolHub::find()->where(['uid'=>$uid])->select(['id'])->asArray()->one()['id'];
                  if ($hub_id){
                  $invites =   HubkolKol::find()->where(['id'=>$kol_id])->select(['invite','invite_number'])->asArray()->one();
                  //查看邀请人数
                  if (!empty($invites['invite'])){
                  $invite = $invites['invite'];
                  $invite_data = json_decode(json_decode($invite,true),true);
                             foreach ($invite_data as $key =>$value){
                                 if ($value['hub_id'] == $hub_id ){
                                     return  HttpCode::renderJSON([],'您已经邀请过了','412');
                                 }
                             }
                             $invite_json = json_decode($invite,true);
                             $bm = str_replace(array('[',']'), array('', ''), $invite_json);
                   }else{
                           $bm = null;
                  }
                         //没有邀请 -》 获取HUB 头像和ID
                         $user_kol['avatar_url']  = $userinfo['avatar_url'];
                         $user_kol['hub_id']  = $hub_id;
                         $add_kol = json_encode($user_kol);
                         if (!$bm){
                             $json_msg   = '['.$bm.$add_kol.']';
                         }else{
                             $json_msg   = '['.$bm.','.$add_kol.']';
                         }
                         //更新网红信息
                         $is_update =   HubkolKol::updateAll(['invite'=>$json_msg,'invite_number'=>$invites['invite_number']+1,'update_time'=>date('Y-m-d H:i:s',time())],['id'=>$kol_id]);
                        //邀请人数
                         if ($is_update){
                             RedisLock::unlock($key);  //清空KEY
                             $transaction->commit();  //提交事务
                             return  HttpCode::renderJSON($userinfo['avatar_url'],'邀请成功','201');
                         }else{
                             RedisLock::unlock($key);  //清空KEY
                             return  HttpCode::renderJSON([],'邀请失败','416');
                         }
                     }else{

                         return  HttpCode::renderJSON([],'请先填写资料','412');
                     }
                  }else{
                         return  HttpCode::renderJSON([],'您不是HUB身份','412');
                  }
              }catch (\ErrorException $e){
                  $transaction->rollBack();
                  throw $e;
              }
            } else{
                return  HttpCode::renderJSON([],'请稍后再试','412');
            }
        }else{
            return  HttpCode::jsonObj([],'请求方式出错','418');
        }
    }

   /*
     * 关注
   */
   public function actionFollow(){
       if ((\Yii::$app->request->isPost)) {
           /*
            * 1.获取该用户角色
            * 2.查看是否填写资料
            * 3.查看是否关注
            */
           $kol_id  = \Yii::$app->request->post('kol_id');
           $status = \Yii::$app->request->post('status')??1;  //0未关注  1已关注
           if (empty($kol_id)){
               return  HttpCode::renderJSON([],'参数不能为空','406');
           }
           //获取用户身份
           $userinfo =   HubkolUser::find()->where(['id'=>$this->uid])->select(['capacity','avatar_url'])->asArray()->one();
           //用户身份为HUB
           if ($userinfo['capacity'] == 1){
               $transaction = \Yii::$app->db->beginTransaction();
               $hub_id = HubkolHub::find()->where(['uid'=>$this->uid])->select(['id'])->asArray()->one()['id'];
               if ($hub_id){
                   //查看是否关注过
                   $follow_status =   HubkolCarefor::find()->where(['kol_id'=>$kol_id,'hub_id'=>$hub_id])->select(['status'])->asArray()->one();

                   if (empty($follow_status['status'])){

                   //没有关注过(插入)
                       $is_success  =   \Yii::$app->db->createCommand()->insert('hubkol_carefor', [
                           'status' => $status,
                           'kol_id' => $kol_id,
                           'hub_id'=>$hub_id
                       ])->execute();
                       if ($is_success){
                           $transaction->commit();
                           return  HttpCode::renderJSON($status,'create is success','201');
                       }else{
                           return  HttpCode::renderJSON([],'error','412');
                       }
                   }else{
                        $cancel_follow =    HubkolCarefor::updateAll(['status'=>$status,'update_time'=>date('Y-m-d H:i:s',time())],['kol_id'=>$kol_id,'hub_id'=>$hub_id]);
                        if ($cancel_follow){
                            $transaction->commit();
                            return  HttpCode::renderJSON($status,'ok','201');
                        }else{
                            return  HttpCode::renderJSON([],'error','412');
                        }
                   }
               }else{
                   return  HttpCode::jsonObj([],'请填写HUB资料','416');
               }
           }else{
               return  HttpCode::jsonObj([],'只有HUB身份才可以关注哦','416');
           }
       }else{
           return  HttpCode::jsonObj([],'请求方式出错','418');
       }
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
