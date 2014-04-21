<?php
namespace Vda\Util\StringModifier;

interface IStringModifier
{
    public function apply($str);
    public function __invoke($str);
}
