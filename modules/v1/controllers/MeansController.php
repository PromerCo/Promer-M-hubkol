<?php
namespace mhubkol\modules\v1\controllers;
use backend\models\HubKolPush;
use mhubkol\common\helps\HttpCode;
use mhubkol\modules\v1\models\HubkolHub;
use mhubkol\modules\v1\models\HubkolKol;
use mhubkol\modules\v1\models\HubkolTags;
use mhubkol\modules\v1\models\HubkolUser;
use mhubkol\modules\v1\services\ParamsValidateService;
use mhubkol\modules\v1\services\HubkolUserService;

/**miexhibit
 * Site controller
*/
class MeansController extends BaseController
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
    public function actionList(){
        //获取用户ID
        $user_id = 2;
        //找到对应的组
        // 获取 cache 组件
        $cache = \Yii::$app->cache;
        // 判断 key 为 username 的缓存是否存在，有则打印，没有则赋值
        $key = 'username';
        if ($cache->exists($key)) {
            var_dump($cache->get($key));
        } else {
            $cache->set($key, 'marko', 60);
        }
    }
    /*
     * 提交资料
     */
    public function actionMaterial(){
        if ((\Yii::$app->request->isPost)) {
            $data  = \Yii::$app->request->post();
            $params = new ParamsValidateService();
            $transaction = \Yii::$app->db->beginTransaction();
            if (empty($data['type'])){
                $data['type'] = 1;
            }
            switch ($data['type']){
                //HUB
                case 1:
                    $Hub = new HubkolHub();
                    $valid = $params->validate(\Yii::$app->request->post(), [
                        [['wechat', 'phone', 'industry', 'company', 'position_code', 'city','city_code','province','province_code'], 'required'],
                        [['create_date', 'update_time'], 'safe'],
                        [['wechat', 'email', 'industry', 'company', 'brand', 'city'], 'string', 'max' => 30],
                        [['phone'],'match','pattern'=>'/^[1][358][0-9]{9}$/'],
                    ]);
                    if (!$valid) {
                        return  HttpCode::renderJSON([],$params->getErrors(),'412');
                    }
                    try {
                        $id  =  HubkolHub::find()->where(['uid'=>$this->uid])->select(['id'])->one();
                        if (!$id){
                            $data['uid'] = $this->uid;
                            $Hub->setAttributes($data,false);
                            if (!$Hub->save() ) {
                                return  HttpCode::renderJSON([],$Hub->errors,'412');
                            }else{
                                HubkolUser::updateAll([
                                    'update_time'=> date('Y-m-d H:i:s',time()),
                                    'capacity'=>1,
                                ],['id'=>$this->uid]);
                                $transaction->commit();
                                return  HttpCode::renderJSON([],'ok','200');
                            }
                        }else{
                           unset($data['type']);
                           $data['update_time'] = date('Y-m-d H:i:s',time());
                           $is_update =    HubkolHub::updateAll($data,['uid'=>$this->uid]);
                           if ($is_update){
                                $transaction->commit();
                                return  HttpCode::renderJSON([],'ok','200');
                           }else{
                               return  HttpCode::renderJSON([],'update failed','412');
                           }
                        }
                    } catch (\Exception $e) {
                        return  HttpCode::renderJSON([],$e->getMessage(),'412');
                    }
                    break;
                //KOL
                case 2:
                    $Kub = new HubkolKol();
                    $valid = $params->validate(\Yii::$app->request->post(), [
                        [['wechat', 'phone', 'city','platform','tags','account','follow_level','city_code','province','province_code'], 'required'],
                        [['follow_level'], 'string', 'max' => 6],
                        [['phone'],'match','pattern'=>'/^[1][358][0-9]{9}$/'],
                    ]);
                    if (!$valid) {
                        return  HttpCode::renderJSON([],$params->getErrors(),'412');
                    }
                    try {
                        $id  =  HubkolKol::find()->where(['uid'=>$this->uid])->select(['id'])->one();
                        if (!$id){
                            $data['uid'] = $this->uid;
                            $Kub->setAttributes($data,false);
                            if (!$Kub->save()) {
                                return  HttpCode::renderJSON([],$Kub->errors,'412');
                            }else{
                                HubkolUser::updateAll([
                                    'update_time'=> date('Y-m-d H:i:s',time()),
                                    'capacity'=>2,
                                ],['id'=>$this->uid]);
                                $transaction->commit();
                                return  HttpCode::renderJSON([],'ok','200');
                            }
                        }else{
                            unset($data['type']);
                            $data['update_time'] = date('Y-m-d H:i:s',time());
                            $is_update =    HubkolKol::updateAll($data,['uid'=>$this->uid]);
                            if ($is_update){
                                $transaction->commit();
                                return  HttpCode::renderJSON([],'ok','200');
                            }else{
                                return  HttpCode::renderJSON([],'update failed','412');
                            }
                        }
                    } catch (\Exception $e) {
                        return  HttpCode::renderJSON([],$e->getMessage(),'412');
                    }
                    break;
            }
        }else{
            return  HttpCode::renderJSON([],'请求方式出错','418');
        }
    }
    /*
     * 获取我的页面数据
    */
    public function actionMiexhibit(){
          $uid =  $this->uid; //获取用户ID
          $types =  HubkolUser::find()->where(['id'=>$uid])->select('capacity')->asArray()->one(); //查询类型(状态)
          return HubkolUserService::Blocked($types['capacity'],$uid); //返回对应角色数据
    }

    public function actionBlocked(){
        if ((\Yii::$app->request->isPost)) {
         $uid  = $this->uid; //用户ID
       try {
         $type = \Yii::$app->request->post('type'); //类型
         if (empty($type)){
            return  HttpCode::renderJSON([],'参数不存在','412');
         }
         $transaction = \Yii::$app->db->beginTransaction(); //开启事务

         $is_success =  HubkolUser::updateAll(['capacity'=>$type,'update_time'=>date('Y-m-d H:i:s',time())],['id'=>$uid]);
       }catch (\Exception $e) {
           return  HttpCode::renderJSON([],$e->getMessage(),'412');
       }

         if ($is_success){

             $transaction->commit();

            return HubkolUserService::Blocked($type,$uid); //返回对应角色数据
         }else{
             return  HttpCode::renderJSON([],'更新失败','416');
         }
    }else{
            return  HttpCode::renderJSON([],'请求方式出错','418');
    }
    }

    /*
     * 查看报名的人信息
     */
    public function actionEnroll(){

        if ((\Yii::$app->request->isPost)) {
            $push_id = \Yii::$app->request->post('push_id')??35;
            $uid = $this->uid; //获取用户ID
            $types = HubkolUser::find()->where(['id' => $uid])->select('capacity')->asArray()->one(); //查询类型(状态)
            if ($types['capacity'] == 1) {
                //HUB
                $bystander = HubKolPush::find()->where(['id' => $push_id])->select(['enroll'])->asArray()->one();
                if (empty($bystander['enroll'])){
                    $enroll = [];
                }else{
                    $enroll = json_decode(json_decode($bystander['enroll'], true), true);
                }
                foreach ($enroll as $key=>$value){
                   $kol_id = $value['kol_id'];

                   $enroll[$key]['list'] = HubkolKol::findBySql("SELECT hubkol_platform.title as platform_title,hubkol_kol.phone,hubkol_kol.city,
                   hubkol_kol.tags,hubkol_follow.title as follow_title FROM hubkol_kol 
                   LEFT JOIN hubkol_follow ON hubkol_kol.follow_level = hubkol_follow.id
                   LEFT JOIN hubkol_platform ON hubkol_platform.id = hubkol_kol.platform
                   WHERE hubkol_kol.id = $kol_id")->asArray()->one();

                //   $enroll[$key]['list']['tags'] =   HubkolTags::findBySql("SELECT title,id FROM hubkol_tags WHERE id in (".$enroll[$key]['list']['tags'].")")->asArray()->all();
                }

                return HttpCode::renderJSON($enroll, 'ok', '201');
            } else {
                return HttpCode::renderJSON([], '请求类型出错', '418');
            }
        }else{
            return HttpCode::renderJSON([], '请求类型出错', '418');
        }
    }




}
