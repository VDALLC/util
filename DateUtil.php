<?php
namespace Vda\Util;

class DateUtil
{
    public static function monthsList()
    {
        $res = array();
        for ($i = 1 ; $i <= 12 ; $i++) {
            $res[$i] = date('M', mktime(0, 0, 0, $i, 1, 2000));
        }
        return $res;
    }
}
