<?php
namespace mhubkol\controllers\v1;
use yii\web\Controller;

class BaseController extends Controller
{
    protected function renderJSON($data=[], $msg ="ok", $code = 200)
    {
        header('Content-type: application/json');
        echo json_encode(["code" => $code, "msg"   =>  $msg, "data"  =>  $data]);
        return \Yii::$app->end();
    }
}

?>
