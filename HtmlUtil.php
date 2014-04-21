<?php
namespace Vda\Util;

class HtmlUtil
{
    public static function options($data, $value = null, $useKeys = false)
    {
        $res = '';
        if ($useKeys) {
            foreach ($data as $key => $item) {
                $selected = ($value == $key ? 'selected' : '');
                $res .= '<option value="' . htmlspecialchars($key) . '" '
                    . $selected . '>' . htmlspecialchars($item) . '</option>';
            }
        } else {
            foreach ($data as $item) {
                $selected = ($value == $item ? 'selected' : '');
                $res .= '<option value="' . htmlspecialchars($item) . '" '
                    . $selected . '>' . htmlspecialchars($item) . '</option>';
            }
        }
        return $res;
    }
}
