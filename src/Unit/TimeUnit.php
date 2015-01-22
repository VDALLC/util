<?php
namespace Vda\Util\Unit;

final class TimeUnit
{
    const TYPE_SECOND = 0;
    const TYPE_MINUTE = 1;
    const TYPE_HOUR   = 2;
    const TYPE_DAY    = 3;
    const TYPE_WEEK   = 4;
    const TYPE_MONTH  = 5;
    const TYPE_YEAR   = 6;

    private static $seconds = array(
        self::TYPE_SECOND => 1,
        self::TYPE_MINUTE => 60,
        self::TYPE_HOUR   => 3600,
        self::TYPE_DAY    => 86400,
        self::TYPE_WEEK   => 604800,
    );

    private static $typeMnemonic = array(
        self::TYPE_SECOND => 'second',
        self::TYPE_MINUTE => 'minute',
        self::TYPE_HOUR   => 'hour',
        self::TYPE_DAY    => 'day',
        self::TYPE_WEEK   => 'week',
        self::TYPE_MONTH  => 'month',
        self::TYPE_YEAR   => 'year',
    );

    private $value;
    private $type;

    public function __construct($value, $type = self::TYPE_SECOND)
    {
        $this->value = $value;
        $this->type = $type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function to($type)
    {
        if ($type == $this->type) {
            return $this;
        }

        return new self(self::convert($this->value, $this->type, $type), $type);
    }

    public static function convert($value, $fromType, $toType)
    {
        return self::toSeconds($value, $fromType) / self::toSeconds(1, $toType);
    }

    public static function toSeconds($value, $type, $startDate = null)
    {
        if (array_key_exists($type, self::$seconds)) {
            return $value * self::$seconds[$type];
        }

        if (is_null($startDate)) {
            $startDate = time();
        }

        $timeStr = ($value >= 0 ? '+' : '') . $value . ' ' . self::$typeMnemonic[$type];

        return strtotime($timeStr, $startDate) - $startDate;
    }
}
