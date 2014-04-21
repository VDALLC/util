<?php
namespace Vda\Util\ParamStore;

interface IParamStore extends \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Check if parameter is present in store
     *
     * @param mixed $paramName
     * @return boolean
     */
    public function hasParam($paramName);

    /**
     * Delete parameter from the store
     *
     * @param mixed $paramName
     * @return mixed Previous value
     */
    public function delete($paramName);

    /**
     * Put parameter to the store
     *
     * @param mixed $paramName
     * @param mixed $value Previous value
     */
    public function set($paramName, $value);

    /**
     * Fetch parameter from the store
     *
     * @param mixed $paramName
     * @param mixed $default
     */
    public function get($paramName, $default = null);

    /**
     * Fetch parameter from the store and cast it to boolean
     * @param mixed $paramName
     * @param boolean $default
     * @return boolean
     */
    public function getBool($paramName, $default = false);

    /**
     * Fetch parameter from the store and cast it to integer
     * @param mixed $paramName
     * @param integer $default
     * @return integer
     */
    public function getInt($paramName, $default = 0);

    /**
     * Fetch parameter from the store and cast it to double
     * @param mixed $paramName
     * @param double $default
     * @return double
     */
    public function getDouble($paramName, $default = 0.0);

    /**
     * Fetch parameter from the store and cast it to array
     * @param mixed $paramName
     * @param array $default
     * @return array
     */
    public function getArray($paramName, array $default = array());

    /**
     * Fetch parameter as IParamStore
     *
     * @param string $paramName
     * @return IParamStore
     */
    public function getSection($paramName);

    /**
     * Fetch parameter from the store, cast it to array and map callback to it
     * @param mixed $paramName
     * @param callable $mapper - callback to apply to every member of result array
     * @param array $default
     * @return array
     */
    public function getMappedArray($paramName, $mapper, array $default = array());

    /**
     * Add all collection elements to this store
     *
     * @param array|\Traversable $data
     */
    public function addAll($data);

    /**
     * Return stored data as array
     *
     * @return array
     */
    public function toArray();

    /**
     * @param string $paramName key of array
     * @param mixed $value value to add to array
     */
    public function push($paramName, $value);

    public function pop($paramName);
}
