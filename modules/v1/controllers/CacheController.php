<?php
namespace mhubkol\modules\v1\controllers;

use mhubkol\common\components\Redis;
use mhubkol\common\helps\HttpCode;
use mhubkol\modules\v1\models\HubkolFollow;
use mhubkol\modules\v1\models\HubkolPlatform;
use mhubkol\modules\v1\models\HubkolPosition;
use mhubkol\modules\v1\models\HubkolTags;
use mhubkol\modules\v1\models\HubkolVersion;
use yii\web\Controller;

/**
 * Site controller
 */
class CacheController extends Controller
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
     * 检验当前版本号
     */
    public function actionVersion()
    {

        if ((\Yii::$app->request->isPost)) {
            $version =  \Yii::$app->request->post('version');

            $valid = HubkolVersion::find()->select(['version'])->asArray()->one();
            if ($version != $valid['version']){
                return  HttpCode::jsonObj(1,'ok',200);  //更新
            }else{
                return  HttpCode::jsonObj(0,'ok',200); //未更新
            }

        }else{
            return  HttpCode::jsonObj([],'请求方式出错','418');
        }
    }

    /*
     * 获取Redis 缓存
     */
    public function  actionMessage(){
        if ((\Yii::$app->request->isPost)) {
        // 查询当前 版本号
        //(版本号,状态)
        $valids = HubkolVersion::find()->select(['version','status'])->asArray()->one();
        //粉丝数目
        $fans = HubkolFollow::find()->select(['id','title'])->asArray()->all();
        //标签
        $tages    =  HubkolTags::find()->select(['id','title'])->asArray()->all();
        //职位
        $position = HubkolPosition::find()->select(['id','code','name','parent_code'])->asArray()->all();
        //领域
        $ploform  = HubkolPlatform::find()->with('retion')->select(['id','title','logo'])->asArray()->all();

        foreach ($ploform as $key =>$value){
                foreach ($value['retion'] as $k =>$v){
                    $ploform[$key]['retion'][$k]   = HubkolTags::find()->where(['id'=>$v['tags_id']])->select(['title','id'])->asArray()->one();
                }
        }

            $data['valids']  =   $valids;
            $data['tages'] =   $tages;
            $data['position'] =   $position;
            $data['ploform'] =   $ploform;  //平台
            $data['fans'] = $fans;
        /*
         * Redis
         */
            $list =  Redis::get('list');
            if (!$list){
                $list = $data;
                Redis::set('list',$list);
            }

        return  HttpCode::renderJSON($list,'ok',200);

        }else{
            return  HttpCode::renderJSON([],'请求方式出错','418');
        }

    }
}
