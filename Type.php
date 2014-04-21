<?php
namespace Vda\Util;

class Type
{
    //TODO Change this to binary literals after switching to PHP5.4+
    const BOOLEAN    = 0x0001; //0b00000000000001;
    const STRING     = 0x0002; //0b00000000000010;
    const NUMERIC    = 0x1000; //0b01000000000000;
    const INTEGER    = 0x1001; //0b01000000000001;
    const DOUBLE     = 0x1003; //0b01000000000011;
    const DATE       = 0x0003; //0b00000000000011;
    const COLLECTION = 0x2000; //0b10000000000000;
    const AUTO       = 0x0000; //0b00000000000000;
    const DYNAMIC    = 0x0100; //0b00000100000000;

    public static function resolveType($value)
    {
        //TODO Add support for collections
        //TODO Introduce Type::Object?
        if (is_integer($value)) {
            return Type::INTEGER;
        } elseif (is_double($value)) {
            return Type::DOUBLE;
        } elseif (is_bool($value)) {
            return Type::BOOLEAN;
        }

        return Type::STRING;
    }
}
