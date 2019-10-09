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


       $data =   new TmplService($formId);
       $data =  $data->activitySend('张三','o4Eh85X3JlRuYBktXnX1tRerhRwM','2019/10/10','18511587569','西门庆大战洪教头');

       print_r($data);


    }
    public function actionIndex()
    {

        echo ':)';

    }


}
