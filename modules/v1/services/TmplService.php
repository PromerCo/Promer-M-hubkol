<?php
namespace mhubkol\modules\v1\services;

use mhubkol\common\components\HttpClient;
use mhubkol\common\helps\Common;

require_once   __DIR__."./../../../../vendor/tmplmsg/wxBizMsgCrypt.php";


class TmplService  {


    private $token;
    private $encodingAesKey;
    private $appId;
    private $appsecret;
    private $formId;

    public function __construct($formId)
    {
        $this->token = \Yii::$app->params['Token'];
        $this->encodingAesKey = \Yii::$app->params['encodingAesKey'];
        $this->appId = \Yii::$app->params['app_id'];
        $this->appsecret = \Yii::$app->params['app_secret'];
        $this->formId = $formId;

    }


    public function activitySend($userName,$openId,$submit_time,$contact,$activity_name,$page="/pages/home/home"){

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
        $formid = $this->formId;
        $post_data = array (
            // 用户的 openID，可用过 wx.getUserInfo 获取
            "touser"           => $openid,
            // 小程序后台申请到的模板编号
            "template_id"      => $templateid,
            // 点击模板消息后跳转到的页面，可以传递参数
            "page"             => $page,
            // 第一步里获取到的 formID
            "form_id"          => $formid,
            // 数据
            "data"             => $data_arr,
            // 需要强调的关键字，会加大居中显示
            "emphasis_keyword" => "keyword2.DATA"
        );

        $data = json_encode($post_data, true);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token;
        $send_msg =HttpClient::post($url,$data);
        return $send_msg;

    }





}