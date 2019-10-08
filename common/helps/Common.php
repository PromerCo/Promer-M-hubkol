<?php
namespace mhubkol\common\helps;

use yii\web\Controller;

class Common extends Controller
{
   public static function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0;
             $i < $length;
             $i++) {
            $str .= $strPol[rand(0, $max)];
        }

        return $str;
    }


    /*
     * 无限分类
     */
    public static function getTree($data,$code,$level)
    {
        $list =array();
        foreach ($data as $k=>$v){
            if ($v['parent_code'] == $code){
                $v['level']=$level;
                $v['son'] =self::getTree($data,$v['code'],$level+1);
                $list[] = $v;
            }
        }
        return $list;
    }

    /*
     * 时间转换几小时前
     */
    /**
     * 获取已经过了多久
     * PHP时间转换
     * 刚刚、几分钟前、几小时前
     * 今天昨天前天几天前
     * @param  string $targetTime 时间戳
     * @return string
     */
    public  static  function get_last_time($targetTime)
    {
        // 今天最大时间
        $todayLast   = strtotime(date('Y-m-d 23:59:59'));
        $targetTime = strtotime($targetTime);
        $agoTimeTrue = time() - $targetTime;
        $agoTime     = $todayLast - $targetTime;
        $agoDay      = floor($agoTime / 86400);

        if ($agoTimeTrue < 60) {
            $result = '刚刚';
        } elseif ($agoTimeTrue < 3600) {
            $result = (ceil($agoTimeTrue / 60)) . '分钟前';
        } elseif ($agoTimeTrue < 3600 * 12) {
            $result = (ceil($agoTimeTrue / 3600)) . '小时前';
        } elseif ($agoDay == 0) {
            $result = '今天 ' . date('H:i', $targetTime);
        }  else {
            $format = date('Y') != date('Y', $targetTime) ? "Y-m-d H:i" : "m-d H:i";
            $result = date($format, $targetTime);
        }
        return $result;
    }

    public  static  function time_tranx($time,$type=0){
        date_default_timezone_set("PRC");
        $time = strtotime($time);

        if ($type == 1){
            $t= time()-$time;
        }else{
            $t= $time-time();
            if ($t<0){
                return '活动已结束';
            }
        }



        $f=array(
            '31536000'=>'年',
            '2592000'=>'个月',
            '604800'=>'星期',
            '86400'=>'天',
            '3600'=>'小时',
            '60'=>'分钟',
            '1'=>'秒'
        );
        foreach ($f as $k=>$v)    {
            if (0 !=$c=floor($t/(int)$k)) {
                return $c.$v;
            }
        }
    }

    /*
     * 二维数组 取值
     */
   public static function deep_in_array($value, $array) {
        foreach($array as $item) {
            if(!is_array($item)) {
                if ($item == $value) {
                    return true;
                } else {
                    continue;
                }
            }

            if(in_array($value, $item)) {
                return true;
            } else if(self::deep_in_array($value, $item)) {
                return true;
            }
        }
        return false;
    }

}
