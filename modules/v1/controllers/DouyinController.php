<?php
namespace mhubkol\modules\v1\controllers;

use mhubkol\common\helps\HttpCode;
use mhubkol\modules\v1\models\HubkolAbout;
use mhubkol\modules\v1\models\HubkolAnalysis;
use mhubkol\modules\v1\models\HubkolBasicdata;
use mhubkol\modules\v1\models\HubkolBillboard;
use mhubkol\modules\v1\models\HubkolDtdformation;
use mhubkol\modules\v1\models\HubkolGather;
use mhubkol\modules\v1\models\HubkolResemble;
use yii\web\Controller;

/**
 * Site controller
 */
class DouyinController extends Controller
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
     * 榜单列表
     */
    public function  actionIndex(){

        $page = \Yii::$app->request->get('page')??0; //页数
        $pt =  \Yii::$app->request->get('pt')??201949; //时间
        $category = \Yii::$app->request->get('category')??'不限'; //标签
        $platform = \Yii::$app->request->get('platform')??'douyin'; //平台

            if ($category == '不限') {
                 if ($page>80){
                     $data = [];
                 }else{
                     $data =  HubkolBillboard::find()->where([
                         'platform'=>$platform,
                         'pt'=>$pt,
                         'status'=>'1'
                     ])->select(['author_id','rank',
                         'updown','name','avatar','category','platform','pt','fans',
                         'episode_avg_played','episode_avg_thumbs','episode_avg_comments',
                         'interactive_incr','interactive_incr','score'])->asArray()->offset($page)->orderBy( 'id ASC')->limit(20)->all();
                 }

            }else{
                $data =  HubkolBillboard::find()->where([
                    'category'=>$category,
                    'platform'=>$platform,
                    'pt'=>$pt,
                    'status'=>'0'
                ])->select(['author_id','rank',
                    'updown','name','avatar','category','platform','pt','fans',
                    'episode_avg_played','episode_avg_thumbs','episode_avg_comments',
                    'interactive_incr','interactive_incr','score'])->asArray()->offset($page)->orderBy( 'rank ASC')->groupBy('rank')->limit(20)->all();

            }
        return   HttpCode::renderJSON($data,'ok','200');
    }

    /*
     * 榜单详情
     */
    public function  actionDetails(){
        $author_id = \Yii::$app->request->get('author_id',0); //页数
        $basicdata_list = HubkolBasicdata::find()->where(['author_id'=>$author_id])
            ->asArray()->indexBy('author_index')->one();
        $dtdformation_list = HubkolDtdformation::find()->where(['author_id'=>$author_id])
                            ->asArray()->indexBy('author_index')->one();
        $about_list = HubkolAbout::find()->where(['p_author_id'=>$author_id])
            ->asArray()->limit(9)->all();
        $resemble_list = HubkolResemble::find()->where(['pauthor_id'=>$author_id])
            ->asArray()->limit(6)->all();
        $data = [];
        $data['basicdata_list'] = $basicdata_list;
        $data['dtdformation_list'] = $dtdformation_list;
        $data['resemble_list']  = $resemble_list;
        $data['about_list']  = $about_list;
        return   HttpCode::renderJSON($data,'ok','200');
    }

 /*
 * 榜单详情
 */
    public function  actionAlytical(){
        $author_id = \Yii::$app->request->get('author_id',0); //页数
        $alytical_list = HubkolAnalysis::find()->where(['author_id'=>$author_id])
            ->asArray()->all();
        return   HttpCode::renderJSON($alytical_list,'ok','200');
    }

  /*
   * 收集用户资料
  */
    public function  actionGather(){

        if(\Yii::$app->request->post()){
            $data  = \Yii::$app->request->post(); //获取数据
            $transaction = \Yii::$app->db->beginTransaction();
            $gather = new HubkolGather();
            //查看手机号是否填写
            $means =    HubkolGather::find()->where(['phone'=>$data['phone']])->asArray()->one();
            if ($means){
                return  HttpCode::renderJSON([],'该手机号已填写','200');
            }
            $gather->setAttributes($data,false);
            if (!$gather->save()){
                return  HttpCode::renderJSON([],$gather->errors,'412');
            }else{
                $transaction->commit();
                return  HttpCode::renderJSON([],'ok','201');
            }
        } else{
            return  HttpCode::jsonObj([],'请求方式出错','418');
        }

    }




}
?>
