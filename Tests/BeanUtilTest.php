<?php

use Vda\Util\BeanUtil;

class BeanUtilTestClass extends PHPUnit_Framework_TestCase
{
    private $oldErrorReporting;

    public function setUp()
    {
        $this->oldErrorReporting = error_reporting(E_ALL);
    }

    public function tearDown()
    {
        error_reporting($this->oldErrorReporting);
    }

    public function testGetPropertyArray()
    {
        $arr = array('a' => 1, 'b' => 2);
        $this->assertEquals(1, BeanUtil::getProperty($arr, 'a'));
        $this->assertNull(BeanUtil::getProperty($arr, 'c'));
    }

    public function testGetPropertyObject()
    {
        $obj = new stdClass();
        $obj->a = 1;
        $obj->b = 2;
        $this->assertEquals(1, BeanUtil::getProperty($obj, 'a'));
        $this->assertNull(BeanUtil::getProperty($obj, 'c'));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetPropertyInvalidNull()
    {
        BeanUtil::getProperty(null, 'c');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetPropertyInvalidNumber()
    {
        BeanUtil::getProperty(1, 'c');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetPropertyInvalidString()
    {
        BeanUtil::getProperty('a', 'c');
    }

    public function testGetPropertyQuiet()
    {
        $this->assertNull(BeanUtil::getProperty(null, 'c', true));
    }
}
