<?php
namespace Vda\Util;

use RuntimeException;

class BeanUtil
{
    private static $getterPrefix = array('get', 'is');

    /**
     * Get bean property or array field
     *
     * @param mixed $obj array or object to fetch the value from
     * @param string $property name of the field/property
     * @param bool $quiet do not throw exception on invalid $obj
     * @throws RuntimeException
     * @return mixed matching field/property
     */
    public static function getProperty($obj, $property, $quiet = false)
    {
        if (is_array($obj)) {
            return isset($obj[$property]) ? $obj[$property] : null;
        } elseif (is_object($obj)) {
            foreach (self::$getterPrefix as $prefix) {
                $getter = $prefix . ucfirst($property);

                if (method_exists($obj, $getter)) {
                    return $obj->$getter();
                }
            }

            return isset($obj->$property) ? $obj->$property : null;
        } elseif ($quiet) {
            return null;
        }

        throw new RuntimeException("Cannot get property '{$property}' of '{$obj}', type " . gettype($obj));
    }

    /**
     * Get list of bean properties or array keys
     *
     * Exclude list may contain sub-lists to make exclusions on nested properties:
     * <code>
     * $exclude = [
     *     'prop1',
     *     'prop2',
     *     'objProperty' => [
     *         'nestedProp',
     *         'nestedObj' => [
     *             'nestedPropL3'
     *         ]
     *     ]
     * ]
     * </code>
     *
     * @param mixed $obj Object, class name or array
     * @param array $exclude List of properties to exclude from result
     * @throws \InvalidArgumentException
     * @return array:
     */
    public static function listProperties($obj, array $exclude = array())
    {
        if (is_array($obj)) {
            $result = array_keys($obj);
        } elseif (is_object($obj) || is_string($obj)) {
            $result = array();
            $class = new \ReflectionClass($obj);

            foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $p) {
                if (!$p->isStatic()) {
                    $result[] = $p->getName();
                }
            }

            foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $m) {
                if ($m->isStatic() || $m->getNumberOfParameters() > 0) {
                    continue;
                }

                foreach (self::$getterPrefix as $prefix) {
                    if (strpos($m->getName(), $prefix) === 0) {
                        $result[] = lcfirst(substr($m->getName(), strlen($prefix)));
                    }
                }
            }
        } else {
            throw new \InvalidArgumentException(
                '$obj param must be either object, string or associative array'
            );
        }

        return array_diff($result, array_filter($exclude, 'is_scalar'));
    }

    /**
     * Convert object to array
     *
     * Converting done in the following maner:
     *  1. all public properties are exported to result array
     *  2. all properties having public getters (methods without parameters,
     *     started with 'get' or 'is) are exported to result array. The name of
     *     property is restored from getter name.
     * This process is recursive, so all property values that are objects will
     * be converted as well.
     *
     * @param mixed $obj Object or array to convert, optinally excluding some properties
     * @param array $exclude List of properties to exclude from result
     * @return array Converted object
     * @see BeanUtil::listProperties()
     */
    public static function toArray($obj, array $exclude = array())
    {
        $result = array();

        foreach (self::listProperties($obj, $exclude) as $property) {
            $value = self::getProperty($obj, $property);

            if (is_object($value) || is_array($value)) {
                if (isset($exclude[$property])) {
                    $value = self::toArray($value, $exclude[$property]);
                } else {
                    $value = self::toArray($value);
                }
            }

            $result[$property] = $value;
        }

        return $result;
    }

    /**
     * Fill target object with values from array.
     *
     * If target is a class name then creates an instance using default constructor.
     * If target is an instance of IImportable, then just calls target's importState() method.
     * Sets target's properties one by one otherwise.
     *
     * @param array $values Property values for the new instance
     * @param string|object $target Either class name as string or object.
     * @param boolean $isStrict If false will silently attempt to assign value to undefined property.
     * @throws \UnexpectedValueException Thrown if property must be instance of some class, but given value is not array or on attempt to assign value to undefined property in strict mode
     * @return object Instance of requested class
     */
    public static function fromArray(array $values, $target = 'stdClass', $isStrict = false)
    {
        if (is_string($target)) {
            $target = new $target();
        }

        if ($target instanceof IImportable) {
            $target->importState($values);
            return $target;
        }

        $methods = get_class_methods($target);
        $properties = get_object_vars($target);

        foreach ($values as $name => $value) {
            $method = 'set' . ucfirst($name);

            if (in_array($method, $methods)) {
                $target->$method($value);
            } elseif (array_key_exists($name, $properties) || !$isStrict) {
                $target->$name = $value;
            } else {
                $class = get_class($target);
                throw new \UnexpectedValueException(
                    "Property '{$name}' is not defined in class '{$class}'"
                );
            }
        }

        return $target;
    }

    /**
     * Convert object or array to json representation
     *
     * @param mixed $obj Object or array to convert
     * @param array $exclude List of properties to exclude from result
     * @return string
     * @see BeanUtil::toArray()
     */
    public static function toJson($obj, array $exclude = array())
    {
        return json_encode(self::toArray($obj, $exclude));
    }

    public static function fromJson($json, $target = 'stdClass', $isStrict = false)
    {
        if ($target == 'stdClass' && !$isStrict) {
            return json_decode($json);
        }

        return self::fromArray(json_decode($json, true), $target, $isStrict);
    }
}
