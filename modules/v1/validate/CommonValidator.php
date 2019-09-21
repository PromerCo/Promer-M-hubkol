<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/3 0003
 * Time: 18:04
 */
namespace apiminip\modules\v1\validate;

use yii\validators\Validator;

class CommonValidator extends Validator {

    public function init()
    {
        parent::init();
        $this->message = 'Invalid status input.';
    }

}