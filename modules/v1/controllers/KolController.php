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

            if ($platform_id == 100000){
                $result = HubkolKol::findBySql("SELECT hubkol_user.avatar_url,hubkol_kol.tags,hubkol_kol.id,
hubkol_user.nick_name,hubkol_follow.title,hubkol_kol.mcn_organization,hubkol_kol.city,
hubkol_platform.logo,hubkol_platform.id as platform_id  FROM  hubkol_kol
LEFT JOIN hubkol_user ON hubkol_kol.uid = hubkol_user.id
LEFT JOIN  hubkol_follow ON  hubkol_follow.id = hubkol_kol.follow_level
LEFT JOIN hubkol_platform ON hubkol_platform.id = hubkol_kol.platform
 WHERE hubkol_user.id  != $this->uid ORDER BY hubkol_kol.create_date DESC
 LIMIT $start_page,5")->asArray()->all();
            }else{
                $result = HubkolKol::findBySql("SELECT hubkol_user.avatar_url,hubkol_kol.tags,hubkol_kol.id,
hubkol_user.nick_name,hubkol_follow.title,hubkol_kol.mcn_organization,hubkol_kol.city,
hubkol_platform.logo,hubkol_platform.id as platform_id  FROM  hubkol_kol
LEFT JOIN hubkol_user ON hubkol_kol.uid = hubkol_user.id
LEFT JOIN  hubkol_follow ON  hubkol_follow.id = hubkol_kol.follow_level
LEFT JOIN hubkol_platform ON hubkol_platform.id = hubkol_kol.platform where  hubkol_kol.platform = $platform_id
 AND   hubkol_user.id  != $this->uid ORDER BY hubkol_kol.create_date DESC
 LIMIT $start_page,5")->asArray()->all();
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
LEFT JOIN hubkol_platform ON hubkol_platform.id = hubkol_kol.platform where  hubkol_kol.platform = $platform_id 
AND   hubkol_user.id  != $this->uid ORDER BY hubkol_kol.create_date DESC
LIMIT $start_page,5")->asArray()->all();
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
                $data =    HubkolPush::findBySql("SELECT  hubkol_push.id,hubkol_push.title,hubkol_platform.logo,hubkol_push.create_date,IF( hubkol_push.expire_time > NOW(),'活动进行中','活动已结束') as activity_status FROM  hubkol_push
LEFT JOIN  hubkol_hub ON hubkol_push.hub_id = hubkol_hub.id
LEFT JOIN  hubkol_platform ON hubkol_platform.id = hubkol_push.platform
WHERE hubkol_hub.uid =$uid 
ORDER BY hubkol_push.create_date desc")->asArray()->all();
                return  HttpCode::renderJSON($data,'ok','201');
            break;
            case 2:
            $data =    HubkolPull::findBySql("SELECT  hubkol_push.id,hubkol_push.title,hubkol_platform.logo,hubkol_pull.is_enroll,hubkol_push.create_date,IF( hubkol_push.expire_time > NOW(),'活动进行中','活动已结束') as activity_status FROM  hubkol_pull 
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
      $pro_id =  \Yii::$app->request->post('pro_id'); //kol
      if (empty($pro_id)){
          return  HttpCode::jsonObj([],'参数不能为空','412');
      }
      $data   =  HubkolKol::findBySql("SELECT hubkol_user.avatar_url,hubkol_kol.city,hubkol_kol.mcn_organization,hubkol_kol.tags,hubkol_kol.id,hubkol_kol.invite,hubkol_kol.invite_number,hubkol_user.id as user_id,
hubkol_user.nick_name,hubkol_follow.title,hubkol_kol.`profile`,hubkol_kol.follow_number FROM hubkol_kol 
LEFT JOIN hubkol_user ON hubkol_user.id = hubkol_kol.uid
LEFT JOIN hubkol_follow ON hubkol_kol.follow_level = hubkol_follow.id
WHERE hubkol_kol.id = $pro_id")->asArray()->one();
      //查看是否关注
      $u_id = HubkolKol::find()->where(['id'=>$pro_id])->select(['uid'])->asArray()->one()['uid'];
      $follow =   HubkolCarefor::find()->where(['kol_id'=>$u_id,'hub_id'=>$this->uid])->select(['status'])->asArray()->one();
      if (empty($follow['status'])){
                $data['status'] = 0;
       }else{
                $data['status'] = $follow['status'];
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
                  $hub_id = HubkolHub::find()->where(['uid'=>$uid])->select(['id'])->asArray()->one();
                  if ($hub_id['id']){
                  $invites =   HubkolKol::find()->where(['id'=>$kol_id])->select(['invite','invite_number'])->asArray()->one();
                  //查看邀请人数
                  if (!empty($invites['invite'])){
                  $invite = $invites['invite'];
                  $invite_data = json_decode(json_decode($invite,true),true);
                             foreach ($invite_data as $key =>$value){
                                 if ($value['hub_id'] == $hub_id['id'] ){

                                     return  HttpCode::renderJSON([],'您已经邀请过了','200');
                                 }
                             }
                             $invite_json = json_decode($invite,true);
                             $bm = str_replace(array('[',']'), array('', ''), $invite_json);
                   }else{
                           $bm = null;
                  }
                         //没有邀请 -》 获取HUB 头像和ID
                         $user_kol['avatar_url']  = $userinfo['avatar_url'];
                         $user_kol['hub_id']  = $hub_id['id'];
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
            * 1.获取该用户身份
            * 2.查看是否填写资料
            * 3.查看是否关注
            */
           $user_id  = \Yii::$app->request->post('user_id');  //关注人ID
           $status = \Yii::$app->request->post('status')??1;  //0未关注  1已关注
           if (empty($user_id)){
               return  HttpCode::renderJSON([],'参数不能为空','406');
           }
          $transaction = \Yii::$app->db->beginTransaction();
          //查看是否关注过
          $follow_status =   HubkolCarefor::find()->where(['kol_id'=>$user_id,'hub_id'=>$this->uid])->select(['status'])->asArray()->one();
          //查看网红关注总人数
          $follow_number = HubkolKol::find()->where(['uid'=>$user_id])->select(['follow_number'])->asArray()->one()['follow_number'];

          if (!$follow_status){
                   //没有关注过(插入)
                       $is_success  =   \Yii::$app->db->createCommand()->insert('hubkol_carefor', [
                           'status' => $status,
                           'kol_id' => $user_id,
                           'hub_id'=>$this->uid
                       ])->execute();

                       if ($is_success){
                           HubkolKol::updateAll(['follow_number'=>$follow_number+1,'update_time'=>date('Y-m-d H:i:s',time())],['uid'=>$user_id]);
                           $transaction->commit();
                           return  HttpCode::renderJSON($status,'create is success','201');
                       }else{
                           return  HttpCode::renderJSON([],'error','412');
                       }
                   }else{
                        $cancel_follow =    HubkolCarefor::updateAll(['status'=>$status,'update_time'=>date('Y-m-d H:i:s',time())],['kol_id'=>$user_id,'hub_id'=>$this->uid]);
                        if ($cancel_follow){
                            if ($status == 1){
                                HubkolKol::updateAll(['follow_number'=>intval($follow_number)+1,'update_time'=>date('Y-m-d H:i:s',time())],['uid'=>$user_id]);
                            }else{
                                HubkolKol::updateAll(['follow_number'=>intval($follow_number)-1,'update_time'=>date('Y-m-d H:i:s',time())],['uid'=>$user_id]);
                            }

                            $transaction->commit();
                            return  HttpCode::renderJSON($status,'ok','201');
                        }else{
                            return  HttpCode::renderJSON([],'error','412');
                        }
                   }
       }else{
           return  HttpCode::renderJSON([],'请求方式出错','418');
       }
   }

   /*
    * 我关注（粉丝）
    */
   public function actionFoluser(){
       $type = \Yii::$app->request->post('type');
       if ($type == 0){
           //关注
           $data =   HubkolUser::findBySql("SELECT avatar_url,nick_name,IF(capacity = 1,'HUB','KOL') as capacity,id FROM  hubkol_user WHERE  id  in(SELECT kol_id FROM hubkol_carefor WHERE hub_id = $this->uid and  status = 1)")->asArray()->all();
           if (!empty($data)){
               foreach ($data as $key => $value){
                   $data[$key]['pro_id'] = HubkolKol::find()->where(['uid'=>$value['id']])->select(['id'])->one()['id'];
               }
           }else{
               $data = [];
           }

       }else{
           //粉丝
           $data =   HubkolUser::findBySql("SELECT avatar_url,nick_name,IF(capacity = 1,'HUB','KOL') as capacity,id FROM  hubkol_user WHERE  id in(SELECT hui_id FROM hubkol_carefor WHERE kol_id = $this->uid  and status = 1)")->asArray()->all();
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
