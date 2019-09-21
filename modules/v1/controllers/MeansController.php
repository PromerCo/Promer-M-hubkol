<?php
namespace apiminip\modules\v1\controllers;
use apiminip\common\helps\HttpCode;
use apiminip\models\HubkolHub;
use apiminip\models\HubkolKol;
use apiminip\models\WechatUser;
use apiminip\services\ParamsValidateService;
use apiminip\services\WechatUserService;

/**
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
                        [['position_code'], 'string', 'max' => 30],
                        [['profile'], 'string', 'max' => 100]
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
                                WechatUser::updateAll([
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
                        [['wechat', 'phone', 'city','platform','tags','account','follow_level','email','city_code','province','province_code'], 'required'],
                        ['email', 'email'],
                        [['profile'], 'string', 'max' => 100],
                        [['follow_level'], 'string', 'max' => 6],
                        [['phone'],'match','pattern'=>'/^[1][358][0-9]{9}$/'],
                        [['content'], 'string'],
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
                                WechatUser::updateAll([
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
          $types =  WechatUser::find()->where(['id'=>$uid])->select('capacity')->asArray()->one(); //查询类型
          return WechatUserService::Blocked($types['capacity'],$uid); //返回对应角色数据
    }

    public function actionBlocked(){
        if ((\Yii::$app->request->isPost)) {
         $uid  = $this->uid;
         $type = \Yii::$app->request->post('type');
         $types = $type+1;
         if (empty($types)){
                return  HttpCode::renderJSON([],'参数不存在','412');
         }
         $transaction = \Yii::$app->db->beginTransaction(); //开启事务
         $is_success =  WechatUser::updateAll(['capacity'=>$types,'update_time'=>date('Y-m-d H:i:s',time())],['id'=>$uid]);
         if ($is_success){
             $transaction->commit();
             return WechatUserService::Blocked($types,$uid); //返回对应角色数据
         }else{
             return  HttpCode::renderJSON([],'更新失败','416');
         }
    }else{
            return  HttpCode::renderJSON([],'请求方式出错','418');

    }
    }
}