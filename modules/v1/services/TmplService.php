<?php
namespace mhubkol\modules\v1\services;

use abei2017\mini\tmpl\Tmpl;

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
    public function send(){
        $pc = new \WXBizMsgCrypt($this->token, $this->encodingAesKey,  $this->appId);
        $encryptMsg = '测试发送';
        $timeStamp = "1409304348";
        $nonce = "2535181275";
        $text = "<xml><ToUserName><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";
        $errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);
        if ($errCode == 0) {
            print("加密后: " . $encryptMsg . "\n");
        } else {
            print($errCode . "\n");
        }

        $xml_tree = new \DOMDocument();
        $xml_tree->loadXML($encryptMsg);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $array_s = $xml_tree->getElementsByTagName('MsgSignature');
        $encrypt = $array_e->item(0)->nodeValue;
        $msg_sign = $array_s->item(0)->nodeValue;
        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);
        $msg = '测试接收';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
        if ($errCode == 0) {
            print("解密后: " . $msg . "\n");
        } else {
            print($errCode . "\n");
        }
    }

    public function activitySend(){

        $access_token =   $this->getAccessToken($this->appId,$this->appsecret);

        $value = '测试';
        $color = '#FF0000';
        $data_arr = array(
            'keyword1' => array( "value" => $value, "color" => $color ),
            'keyword2' => array( "value" => $value, "color" => $color ),
            'keyword3' => array( "value" => $value, "color" => $color ),
            'keyword4' => array( "value" => $value, "color" => $color )
        );

        $openid = 'o4Eh85X3JlRuYBktXnX1tRerhRwM';
        $templateid = 'ePAuWztIxOdb3S-9OW6eE0AfEyT0VTY1NuYiFEwmH3A';

        if (empty($this->formId)){
            return false;
        }

        $formid = $this->formId;
        $post_data = array (
            // 用户的 openID，可用过 wx.getUserInfo 获取
            "touser"           => $openid,
            // 小程序后台申请到的模板编号
            "template_id"      => $templateid,
            // 点击模板消息后跳转到的页面，可以传递参数
            "page"             => "/pages/app/init/main",
            // 第一步里获取到的 formID
            "form_id"          => $formid,
            // 数据
            "data"             => $data_arr,
            // 需要强调的关键字，会加大居中显示
            "emphasis_keyword" => "keyword2.DATA"

        );

        $data = json_encode($post_data, true);

        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token;

        $return = $this->send_post( $url, $data);

        print_r($return);


    }

     public function send_post( $url, $post_data ) {
        $options = array(
            'http' => array(
                'method'  => 'POST',
                // header 需要设置为 JSON
                'header'  => 'Content-type:application/json',
                'content' => $post_data,
                // 超时时间
                'timeout' => 60
            )
        );

        $context = stream_context_create( $options );
        $result = file_get_contents( $url, false, $context );

        return $result;
    }

    public function getAccessToken ($appid, $appsecret) {
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
        $html = file_get_contents($url);
        $output = json_decode($html, true);
        $access_token = $output['access_token'];
        return $access_token;
    }




}