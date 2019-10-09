<?php
namespace mhubkol\modules\v1\services;

use abei2017\mini\tmpl\Tmpl;

require_once "../../../../vendor/tmplmsg/wxBizMsgCrypt.php";


class TmplService  {

    private $token;
    private $encodingAesKey;
    private $appId;

    public function __construct()
    {
        $this->token = \Yii::$app->params['Token'];
        $this->encodingAesKey = \Yii::$app->params['encodingAesKey'];
        $this->appId = \Yii::$app->params['app_id'];

        $pc = new \WXBizMsgCrypt($this->token, $this->encodingAesKey,  $this->appId);

        print_r($pc);
    }



}