<?php
namespace Vda\Util;

class StringUtil
{
    public static function fillParams($str, array $params, $prefix = '#', $suffix = '#')
    {
        $replace = array();

        foreach ($params as $k => $v) {
            $replace[$prefix . $k . $suffix] = $v;
        }

        return strtr($str, $replace);
    }
}
