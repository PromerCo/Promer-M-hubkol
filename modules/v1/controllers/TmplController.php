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
       $data =   new TmplService();

       print_r($data);


    }
    public function actionIndex()
    {

        echo ':)';

    }


}
