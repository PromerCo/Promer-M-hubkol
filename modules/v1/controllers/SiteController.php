<?php
namespace apiminip\modules\v1\controllers;
use yii\web\Controller;
use Yii;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $modelClass = 'apiminip\modules\v1\models\guide';
    /**
     * @inheritdoc  验证码
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

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $getParams  =  Yii::$app->request;
        print_r($getParams->get('id'));
    }

    public function actionText()
    {

      echo '测试';
    }


}
