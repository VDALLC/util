<?php
use Vda\Util\ParamStore\ParamStore;

class ParamStoreTestClass extends PHPUnit_Framework_TestCase
{
    /**
     * @var ParamStore
     */
    protected $paramStore;

    public function setUp()
    {
        $arr = self::getTestData();
        $this->paramStore = new ParamStore($arr);
    }

    public static function getTestData()
    {
        return array(
            'qwe' => array('a' => 1, 'b' => 2, 'c' => 3),
            'asd' => 4,
            'zxc' => 5,
        );
    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->paramStore['asd']));
        $this->assertTrue(!empty($this->paramStore['asd']));
        $this->assertTrue($this->paramStore->offsetExists('asd'));
        $this->assertFalse(array_key_exists('asd', $this->paramStore)); // !!

        $this->assertFalse(isset($this->paramStore['not']));
        $this->assertTrue(empty($this->paramStore['not']));
        $this->assertFalse($this->paramStore->offsetExists('not'));
        $this->assertFalse(array_key_exists('not', $this->paramStore));
    }

    public function testReferencedOffsetGet()
    {
        $this->assertEquals(1, $this->paramStore['qwe']['a']);
        $this->paramStore['qwe']['a'] = 6;
        $this->assertEquals(6, $this->paramStore['qwe']['a']);

        $this->assertEquals(3, $this->paramStore['qwe']['c']);
        $var = $this->paramStore['qwe'];
        $var['c'] = 6;
        $this->assertEquals(3, $this->paramStore['qwe']['c']);
    }

    public function testOffsetSet()
    {
        $this->assertEquals(4, $this->paramStore['asd']);
        $this->paramStore['asd'] = 5;
        $this->assertEquals(5, $this->paramStore['asd']);

        $this->assertEquals(5, $this->paramStore['zxc']);
        $this->paramStore['zxc']++;
        $this->assertEquals(6, $this->paramStore['zxc']);

        $this->paramStore[] = 5;
        $this->assertEquals(5, $this->paramStore[0]);
    }

    public function testOffsetUnset()
    {
        $this->assertEquals(4, $this->paramStore['asd']);
        unset($this->paramStore['asd']);
        $this->assertEquals(null, $this->paramStore['asd']);
    }

    public function testGetIterator()
    {
        $this->assertInstanceOf('Traversable', $this->paramStore->getIterator());

        $keys = '';
        foreach ($this->paramStore as $k => $v) {
            $keys .= $k;
        }
        $this->assertEquals('qweasdzxc', $keys);
    }

    public function testCount()
    {
        $this->assertEquals(3, count($this->paramStore));
        $this->paramStore[] = 4;
        $this->paramStore[] = 5;
        $this->assertEquals(5, count($this->paramStore));
    }

    public function testHasParam()
    {
        $this->assertTrue($this->paramStore->hasParam('qwe'));
        $this->assertFalse($this->paramStore->hasParam('not'));
    }

    public function testDelete()
    {
        $this->assertTrue($this->paramStore->hasParam('asd'));
        $old = $this->paramStore->delete('asd');
        $this->assertFalse($this->paramStore->hasParam('asd'));
        $this->assertEquals(4, $old);
    }

    public function testSet()
    {
        $this->assertEquals(4, $this->paramStore['asd']);
        $old = $this->paramStore->set('asd', 5);
        $this->assertEquals(5, $this->paramStore['asd']);
        $this->assertEquals(4, $old);
    }

    public function testGet()
    {
        $this->assertEquals(4, $this->paramStore->get('asd'));

        $this->assertFalse($this->paramStore->hasParam('not'));
        $this->assertEquals(null, $this->paramStore->get('not'));
        $this->assertEquals('default', $this->paramStore->get('not', 'default'));

        $this->paramStore['asd'] = null;
        $this->assertTrue($this->paramStore->hasParam('asd'));
        $this->assertEquals(null, $this->paramStore->get('asd', 'default'));
    }

    public function testGetBool()
    {
        $this->paramStore['a'] = '123.45qwe';
        $this->assertTrue($this->paramStore->getBool('a'));
    }

    public function testGetInt()
    {
        $this->paramStore['a'] = '123.45qwe';
        $this->assertEquals(123, $this->paramStore->getInt('a'));
    }

    public function testGetDouble()
    {
        $this->paramStore['a'] = '123.45qwe';
        $this->assertEquals(123.45, $this->paramStore->getDouble('a'));
    }

    public function testGetArray()
    {
        $this->paramStore['a'] = 1;
        $this->assertEquals(array(1), $this->paramStore->getArray('a'));
    }

    public function testGetSection()
    {
        $section = $this->paramStore->getSection('qwe');
        $this->assertInstanceOf('Vda\Util\ParamStore\IParamStore', $section);
    }

    public function testGetMappedArray()
    {
        $res = $this->paramStore->getMappedArray('qwe', function($v) { return $v + 1; });
        $this->assertEquals(array('a' => 2, 'b' => 3, 'c' => 4), $res);
    }

    public function testAddAll()
    {
        $this->assertFalse($this->paramStore->hasParam('a'));
        $this->assertFalse($this->paramStore->hasParam('b'));
        $this->paramStore->addAll(array(
            'a' => 1,
            'b' => 2,
        ));
        $this->assertEquals(1, $this->paramStore['a']);
        $this->assertEquals(2, $this->paramStore['b']);
    }

    public function testToArray()
    {
        $keys = array_keys($this->paramStore->toArray());
        $this->assertEquals(array('qwe', 'asd', 'zxc'), $keys);
    }

    public function testPush()
    {
        unset($this->paramStore['qwe']);
        $this->assertFalse($this->paramStore->hasParam('qwe'));
        $this->paramStore->push('qwe', 1);
        $this->paramStore->push('qwe', 2);
        $this->assertEquals(array(1, 2), $this->paramStore['qwe']);
    }
}
