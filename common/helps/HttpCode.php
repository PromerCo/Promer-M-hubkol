<?php
namespace mhubkol\common\helps;

use yii\web\Controller;

class HttpCode extends Controller{

    /*
     *  json
     */
  public static function renderJSON($data=[], $msg ="ok", $code = 200)
    {
      header('Content-type: application/json');
        return  json_encode([
            "code" => $code,
            "msg"   =>  $msg,
            "data"  =>  $data,
            "req_id" =>  uniqid()
        ]);
        return     \Yii::$app->end();
    }
    /*
     * 对象
     */
    public static function jsonObj($data=[], $msg ="ok", $code = 200)
    {
        header('Content-type: application/json');
        echo   json_encode([
            "code" => $code,
            "msg"   =>  $msg,
            "data"  =>  $data,
            "req_id" =>  uniqid()
        ]);
        return     \Yii::$app->end();
    }
}
