<?php
namespace mhubkol\common\components;

use yii\web\Controller;

class BaseWebController extends Controller
{
    public $enableCsrfValidation = false;

	public function post($key, $default = "") {
		return \Yii::$app->request->post($key, $default);
	}

	public function get($key, $default = "") {
		return \Yii::$app->request->get($key, $default);
	}







}
