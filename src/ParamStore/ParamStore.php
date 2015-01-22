<?php
namespace Vda\Util\ParamStore;

class ParamStore extends AbstractParamStore
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array & $data = array(), $isReference = false)
    {
        if ($isReference) {
            $this->data = & $data;
        } else {
            $this->data = $data;
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    public function & offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function toArray()
    {
        return $this->data;
    }

    public function count()
    {
        return count($this->data);
    }
}
