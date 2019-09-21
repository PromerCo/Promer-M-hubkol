<?php
namespace apiminip\modules\v1\controllers;
use apiminip\common\helps\HttpCode;
use apiminip\models\HubkolHub;
use apiminip\models\HubkolKol;
use apiminip\models\HubkolPush;
use apiminip\models\WechatUser;
use apiminip\services\ParamsValidateService;
/**
 * Site controller
 */
class PublishController extends BaseController
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
     * 发布活动(广告组 HUB) - 发布活动  -可能存在问题- 重复点击 插入两条一样活动活动
    */
    public function actionPush(){
        if ((\Yii::$app->request->isPost)) {
            $data  = \Yii::$app->request->post();
            $transaction = \Yii::$app->db->beginTransaction();
            $push = new HubkolPush();
            $hub_id =  HubkolHub::find()->where(['uid'=>$this->uid])->select('id')->asArray()->one();  //外加一个状态 标识切换账号
            if (empty($hub_id) || !$hub_id){
                return  HttpCode::renderJSON([],'请先完善资料','412');
            }else{
                $data['hub_id'] = $hub_id['id'];
            }
            $push->setAttributes($data,false);
            if (!$push->save()){
                return  HttpCode::renderJSON([],$push->errors,'412');
            }else{

                $transaction->commit();
                return  HttpCode::renderJSON($push->id,'ok','201');
            }
        }else{
            return  HttpCode::renderJSON([],'请求方式出错','418');
        }
    }

}