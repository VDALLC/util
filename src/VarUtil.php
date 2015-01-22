<?php
namespace Vda\Util;

class VarUtil
{
    public static function ifNull(&$var, $default = null)
    {
        return is_null($var) ? $default : $var;
    }

    public static function ifEmpty(&$var, $default = null)
    {
        return empty($var) ? $default : $var;
    }
}
