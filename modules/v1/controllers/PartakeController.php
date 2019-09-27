<?php
namespace mhubkol\modules\v1\controllers;
use mhubkol\common\components\RedisLock;
use mhubkol\common\helps\Common;
use mhubkol\common\helps\HttpCode;
use mhubkol\models\HubkolHub;
use mhubkol\models\HubkolKol;
use mhubkol\models\HubkolPull;
use mhubkol\models\HubkolPush;
use mhubkol\modules\v1\models\WechatUser;

/**
 * Site controller
 */
class PartakeController extends BaseController
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
     * 参与报名
    */
    public function actionEnroll(){
    if ((\Yii::$app->request->isPost)) {
            $data  = \Yii::$app->request->post();
            $uid = $this->uid;
            $transaction = \Yii::$app->db->beginTransaction();
            if (empty($data['push_id'])){
                return  HttpCode::renderJSON([],'参数不能为空','406');
            }
            $push_id = $data['push_id'];   //活动 ID
            $key = 'mylock';//加锁
            $is_lock = RedisLock::lock($key);
            if ($is_lock){
                try {
                // 入伍人数  入伍人  召集人数
                $data = HubkolPush::findBySql("SELECT  `convene`,`enroll`,`enroll_number` FROM hubkol_push WHERE id = $push_id FOR UPDATE")->asArray()->one();
                $enroll =$data['enroll']; //入伍人
                $enroll_number =$data['enroll_number']; //入伍人数
                $convene =$data['convene']; //召集人数
               //查看用户是否填写资料
               $means =    HubkolKol::find()->where(['uid'=>$this->uid])->select(['id'])->asArray()->one();
               if (!$means){
                   return  HttpCode::renderJSON([],'请先填写资料','406');
               }
              //假如用户填写资料
              $is_pull =   HubkolPull::find()->where(['push_id'=>$push_id,'kol_id'=>$means['id']])->asArray()->count();
               $material =  WechatUser::find()->where(['id'=>$uid])->select(['capacity'])->asArray()->one();  //身份标识（0 未填写资料 1 HUB 2KOL
               if ($material['capacity'] != 2){
                   return  HttpCode::renderJSON([],'您不是KOL身份','412');
               }
               if (!$is_pull){
                    \Yii::$app->db->createCommand()->insert('hubkol_pull', [
                       'bystander_frequency' => '1',
                       'kol_id' => $means['id'],
                       'push_id'=>$push_id
                   ])->execute();
               }
               //报名人数是否达到
               if ($enroll_number > $convene ){
                        RedisLock::unlock($key);  //清空KEY
                        return  HttpCode::renderJSON([],'报名人数已达到','200');
               }
                //用户是否报名
              $enrolls =     HubkolPull::findBySql("SELECT hubkol_pull.is_enroll,hubkol_pull.id as pull_id FROM hubkol_push
LEFT JOIN hubkol_pull ON hubkol_push.id = hubkol_pull.push_id
LEFT JOIN hubkol_kol ON   hubkol_kol.id = hubkol_pull.kol_id
WHERE  hubkol_push.id = $push_id AND   hubkol_kol.uid=$this->uid")->asArray()->one();

              if ($enrolls['is_enroll']){
                  RedisLock::unlock($key);  //清空KEY
                  return  HttpCode::renderJSON([],'您已经报名','200');
              }else{
                  $user_info = WechatUser::find()->where(['id'=>$this->uid])->select(['avatar_url','nick_name','gender','phone_number'])->asArray()->one();
                  $enroll_add['avatar_url'] =  $user_info['avatar_url'];
                  $enroll_add['nick_name'] =  $user_info['nick_name'];
                  $enroll_add['gender'] =  $user_info['gender'];
                  $enroll_add['phone_number'] =  $user_info['phone_number'];
                  $enroll_add['kol_id'] =  HubkolHub::find()->where(['uid'=>$this->uid])->select(['id'])->asArray()->one()['id'];
                  $enroll_add = json_encode($enroll_add);
                  $bm         = json_decode($enroll,true);

                  $bm = str_replace(array('[',']'), array('', ''), $bm);
                  if (!$bm){
                      $json_msg   = '['.$bm.$enroll_add.']';
                  }else{
                      $json_msg   = '['.$bm.','.$enroll_add.']';
                  }

                  //更新报名信息 (后期替换关联更新)
                  $push_update =   HubkolPush::updateAll(['enroll_number'=>$enroll_number+1,'enroll'=>$json_msg,'update_time'=>date('Y-m-d H:i:s',time())],['id'=>$push_id]);
                  $pull_update =    HubkolPull::updateAll(['is_enroll'=>'1','is_success'=>'1','update_time'=>date('Y-m-d H:i:s',time())],['id'=>$enrolls['pull_id']]);
                  if ($push_update && $pull_update){
                      RedisLock::unlock($key);  //清空KEY
                      $transaction->commit();  //提交事务
                      return  HttpCode::renderJSON($user_info['avatar_url'],'报名成功','201');
                  }else{
                      RedisLock::unlock($key);  //清空KEY
                      return  HttpCode::renderJSON([],'报名失败','416');
                  }
              }
                }catch (\ErrorException $e){
                    $transaction->rollBack();
                    throw $e;
                }
            }else{
                echo '请稍后再试';
            }
       }else{
           return  HttpCode::jsonObj([],'请求方式出错','418');
      }
    }


    /*
     * 记录用户浏览量
     */
    public function actionPageviews(){
        $uid = $this->uid;
        $push_id =  \Yii::$app->request->post('push_id');  //发布活动ID
        /*
          * 查看用户是否浏览
        */
        if (empty($push_id)){
            return  HttpCode::renderJSON([],'参数不能为空','406');
        }
        $bystander = HubkolPush::find()->where(['id'=>$push_id])->select(['bystander','bystander_number'])->asArray()->one();
        $bystander_number = $bystander['bystander_number'];
        $transaction = \Yii::$app->db->beginTransaction();

        if (!$bystander['bystander']){
            /*
             * 没有人浏览 （新增一条）
             */
            //存储 ID  （转json）
            $bystander_add['uid'] = $uid;
            $bystander_add = json_encode($bystander_add);
            $json_msg   = '['.$bystander_add.']';
             HubkolPush::updateAll(['bystander_number'=>$bystander_number+1,'bystander'=>$json_msg,'update_time'=>date('Y-m-d H:i:s',time())],['id'=>$push_id]);
        }else{
            $bystander = $bystander['bystander'];
            $bystander = json_decode($bystander);
            $uids      = json_decode($bystander,true);
            $serach_user =  Common::deep_in_array($uid,$uids);  // 搜索用户

            if (!$serach_user){
                $bm = str_replace(array('[',']'), array('', ''), $bystander);
                $bystander_add['uid'] = $uid;
                $bystander_add = json_encode($bystander_add);
                $json_msg   = '['.$bm.','.$bystander_add.']';
                // 用户不存在  （插入一条）
                 HubkolPush::updateAll(['bystander_number'=>$bystander_number+1,'bystander'=>$json_msg,'update_time'=>date('Y-m-d H:i:s',time())],['id'=>$push_id]);
            }
        }
        $kol_id =  HubkolKol::find()->where(['uid'=>$uid])->select(['id'])->asArray()->one()['id'];

        if (empty($kol_id)){
            return  HttpCode::renderJSON([],'资料未填写,不记录','200');
        }
        $create_pull =   HubkolPull::findBySql("SELECT bystander_frequency,id,is_enroll,is_success  FROM hubkol_pull WHERE kol_id = $kol_id AND push_id = $push_id")->asArray()->one();
        if ($create_pull){
           $result =  HubkolPull::updateAll(['bystander_frequency'=>$create_pull['bystander_frequency']+1,'update_time'=>date('Y-m-d H:i:s',time())],['id'=>$create_pull['id']]);
        }else{
            $result =   \Yii::$app->db->createCommand()->insert('hubkol_pull', [
               'bystander_frequency' => '1',
               'kol_id' => $kol_id,
               'push_id'=>$push_id
           ])->execute();
        }

        if ($result){
            $transaction->commit();
            return  HttpCode::renderJSON([],'ok','201');
        }
    }

    /*
     * 收藏
     */
    public function actionCollect(){
        if ((\Yii::$app->request->isPost)) {
            $uid = $this->uid;
            $collect = \Yii::$app->request->post('collect');
            $push_id = \Yii::$app->request->post('push_id');

            $kol_id = HubkolKol::find()->where(['uid' => $uid])->select(['id'])->asArray()->one();

            if (empty($kol_id['id'])) {
                return HttpCode::jsonObj([], '资料不全', '416');
            }
            $transaction = \Yii::$app->db->beginTransaction();
            /*
             * 更新收藏
             */
            $is_update = HubkolPull::updateAll(['is_collect' => $collect, 'update_time' => date('Y-m-d H:i:s', time())], [
                'push_id' => $push_id,
                'kol_id'=>$kol_id['id']
            ]);
            if ($is_update){
                $transaction->commit();
                return  HttpCode::jsonObj($collect,'OK','201');
            }else{
                return  HttpCode::jsonObj([],'error','416');
            }



        }else{
            return  HttpCode::jsonObj([],'请求方式出错','418');
        }

    }


}
