<?php
namespace mhubkol\modules\v1\services;

use abei2017\mini\tmpl\Tmpl;

require_once   __DIR__."./../../../../vendor/tmplmsg/wxBizMsgCrypt.php";


class TmplService  {

    private $token;
    private $encodingAesKey;
    private $appId;

    public function __construct()
    {
        $this->token = \Yii::$app->params['Token'];
        $this->encodingAesKey = \Yii::$app->params['encodingAesKey'];
        $this->appId = \Yii::$app->params['app_id'];
    }
    public function send(){
        $pc = new \WXBizMsgCrypt($this->token, $this->encodingAesKey,  $this->appId);
        $encryptMsg = '';
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
        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
        if ($errCode == 0) {
            print("解密后: " . $msg . "\n");
        } else {
            print($errCode . "\n");
        }






    }



}