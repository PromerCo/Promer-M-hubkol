<<<<<<< HEAD
<?php
namespace mhubkol\modules\v1\controllers;
use common\components\Aliyunoss;
use yii\web\Controller;
/**
 * Site controller
 */
class AliossController extends Controller
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

    public function  actionIndex(){

           $al = new Aliyunoss();
        $al->test();
    }


}
=======
<?php
namespace mhubkol\modules\v1\controllers;
use common\components\Aliyunoss;
use yii\web\Controller;
/**
 * Site controller
 */
class AliossController extends Controller
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

    public function  actionIndex(){

           $al = new Aliyunoss();
        $al->test();
    }


}
>>>>>>> 023f55c9c2306d03c843bf4c73671fb243541e14
