<?php
namespace mhubkol\modules\v1\controllers;
use mhubkol\modules\v1\services\TmplService;
use yii\web\Controller;

/**
 * Site controller
 */
class TmplController extends Controller
{
    public  $enableCsrfValidation=false;

    /**
     * @inheritdoc
     */
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

    public  function actionSignature()
    {
       $formId = \Yii::$app->request->post('form_id');
       //1.更新该用户form_id操作
       //2.查找活动ID




        $tmpl =   new TmplService($formId,41);
        $send_tmpl=  $tmpl->activitySend('zhangsan','o4Eh85X3JlRuYBktXnX1tRerhRwM',$formId,'2019/10/10',18751587568,'西门庆大战洪教头');

        print_r($send_tmpl);


    }
    public function actionIndex()
    {

        echo ':)';

    }


}
