<?php
namespace mhubkol\modules\v1\services;

use mhubkol\common\components\HttpClient;
use mhubkol\common\helps\Common;
use mhubkol\modules\v1\models\HubkolUser;

require_once   __DIR__."./../../../../vendor/tmplmsg/wxBizMsgCrypt.php";


class TmplService  {


    private $token;
    private $encodingAesKey;
    private $appId;
    private $appsecret;
    private $formId;
    private $uid;

    public function __construct($formId,$uid)
    {
        $this->token = \Yii::$app->params['Token'];
        $this->encodingAesKey = \Yii::$app->params['encodingAesKey'];
        $this->appId = \Yii::$app->params['app_id'];
        $this->appsecret = \Yii::$app->params['app_secret'];
        $this->formId = $formId;
        $this->uid = $uid;
        HubkolUser::updateAll(['form_id'=>$this->formId,'update_time'=>date('Y-m-d H:i:s',time())],['id'=>$uid]);

    }


    public function activitySend($userName,$openId,$form_id,$submit_time,$contact,$activity_name,$page="/pages/home/home"){

        $access_token =   Common::getAccessToken($this->appId,$this->appsecret);
        $color = '#FF0000';
        $data_arr = array(
            'keyword1' => array( "value" => $userName, "color" => $color ),   //姓名
            'keyword3' => array( "value" => $contact, "color" => $color ),   //联系方式
            'keyword4' => array( "value" => $activity_name, "color" => $color ), //活动名称
            'keyword2' => array( "value" => $submit_time, "color" => $color )  //提交时间
        );
        $openid = $openId;
        $templateid = \Yii::$app->params['tmpl_activity'];
        $post_data = array (
            "touser"           => $openid,
            "template_id"      => $templateid,
            "page"             => $page,
            "form_id"          => $form_id,
            "data"             => $data_arr,
            "emphasis_keyword" => "keyword2.DATA"
        );

        $data = json_encode($post_data, true);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token;
        $send_msg =HttpClient::post($url,$data);
        return $send_msg;

    }





}