<?php
namespace Vda\Util\StringModifier;

class PrefixModifier implements IStringModifier
{
    private $prefix;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public function apply($str)
    {
        return $this->prefix . $str;
    }

    public function __invoke($str)
    {
        return $this->apply($str);
    }
}
