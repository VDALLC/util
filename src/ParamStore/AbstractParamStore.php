<?php
namespace Vda\Util\ParamStore;

abstract class AbstractParamStore implements IParamStore
{
    public function hasParam($paramName)
    {
        return $this->offsetExists($paramName);
    }

    public function delete($paramName)
    {
        $old = $this->get($paramName);
        $this->offsetUnset($paramName);
        return $old;
    }

    public function set($paramName, $value)
    {
        if (is_null($paramName)) {
            throw new \InvalidArgumentException('$paramName must not be null');
        }

        $old = $this->get($paramName);
        $this->offsetSet($paramName, $value);

        return $old;
    }

    public function get($paramName, $default = null)
    {
        if ($this->offsetExists($paramName)) {
            return $this->offsetGet($paramName);
        } else {
            return $default;
        }
    }

    public function getBool($paramName, $default = false)
    {
        return (boolean) $this->get($paramName, $default);
    }

    public function getInt($paramName, $default = 0)
    {
        return (integer) $this->get($paramName, $default);
    }

    public function getDouble($paramName, $default = 0.0)
    {
        return (double) $this->get($paramName, $default);
    }

    public function getArray($paramName, array $default = array())
    {
        return (array) $this->get($paramName, $default);
    }

    public function getSection($paramName)
    {
        $result = $this->get($paramName);

        if ($result instanceof IParamStore) {
            return $result;
        } elseif (is_array($result)) {
            return new static($result);
        } elseif (is_null($result)) {
            return new static();
        }

        throw new \RuntimeException("The parameter under '{$paramName}' key cannot be fetched as section");
    }

    public function getMappedArray($paramName, $mapper, array $default = array())
    {
        //FIXME Once 5.3 support dropped replaces this with callable typehint
        if (!is_callable($mapper)) {
            throw new \InvalidArgumentException('Mapper must be valid callback');
        }

        return array_map($mapper, $this->getArray($paramName, $default));
    }

    public function addAll($data)
    {
        if (is_array($data) || $data instanceof \Traversable) {
            foreach ($data as $k => $v) {
                $this->offsetSet($k, $v);
            }
        } else {
            throw new \InvalidArgumentException("array or Traversable expected, " . gettype($data)  . ' given');
        }
    }

    public function push($paramName, $value)
    {
        if (!$this->offsetExists($paramName)) {
            $this->offsetSet($paramName, array());
        }
        array_push($this->offsetGet($paramName), $value);
    }

    public function pop($paramName)
    {
        if ($this->offsetExists($paramName)) {
            if (is_array($this->offsetGet($paramName))) {
                return array_pop($this->offsetGet($paramName));
            } else {
                $res = $this->offsetGet($paramName);
                $this->offsetUnset($paramName);
                return $res;
            }
        } else {
            return null;
        }
    }
}
