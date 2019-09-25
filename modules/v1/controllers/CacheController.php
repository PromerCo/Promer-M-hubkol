<?php

namespace mhubkol\modules\v1\controllers;
use mhubkol\common\helps\HttpCode;
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
            $valid = '1.1';
            if ($version != $valid){
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
            $valid = '1.1';
            return  HttpCode::jsonObj(['valid'=>$valid],'ok',200);


        }else{
            return  HttpCode::jsonObj([],'请求方式出错','418');
        }

    }





}
