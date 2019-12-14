<?php

namespace mstevz\collection;

/**
 *
 * @author Miguel Esteves <mstevz@mail.com>
 * @license https://github.com/mstevz/collection/blob/master/LICENSE
 */
class ArrayList extends \ArrayObject {

    /**
     * Item values.
     * @var array
     */
    protected $container;

    /**
     * Size of the container.
     * @var int
     */
    private $length;

    /**
     * Value type of the items at container.
     * @var string
     */
    private $typeOfList;

    /**
     * If list is type restricted or not.
     * @var bool
     */
    private $isValueTypeRestricted;


    public function __construct(string $type = '*'){
        $this->clear();

        if($this->isValidType($type)){
            $this->typeOfList = $type;

            // Define if List should be value type mixed or not.
            $this->isValueTypeRestricted = ($type == '*') ? false : true;
        }
    }

    /**
     * Fixes the index numbers of the items.
     * This is useful when removing an offset at middle.
     */
    private function rebaseIndexes() {
        $this->container = array_values($this->container);
    }

    /**
     * Checks weather the provided type is php valid.
     * @param  string $type
     * @return bool
     */
    protected function isValidType(string $type) : bool {

        $isValidType = null;

        switch($type) {
            case '*':
            case 'boolean':
            case 'integer':
            case 'float':
            case 'array':
            case 'object':
            case 'resource':
                $isValidType = true;
                break;
            default:
                $isValidType = false;
                break;
        }

        return $isValidType;
    }

    /**
     * Clears the ArrayList
     */
    public function clear(){
        $this->container = array();
        $this->length = 0;
    }

    /**
     * Adds a new value
     * @param  mixed    $value
     * @return ArrayList
     */
    public function add($value) : ArrayList {

        if($this->isValueTypeRestricted){
            if($this->typeOfList != gettype($value)){
                throw new \InvalidArgumentException('Invalid value type given.');
            }
        }

        array_push($this->container, $value);
        $this->length++;

        return $this;
    }

    /**
     * Attempts to retrieve the requested offset.
     * @param  int    $offset [description]
     * @throws \OutOfBoundsException
     * @return mixed
     */
    public function get(int $offset){

        if(!isset($this->container[$offset])){
            throw new \OutOfBoundsException();
        }

        return $this->container[$offset];
    }

    /**
     * Attempts to remove the requested offset.
     * @param  int    $offset
     * @throws \OutOfBoundsException
     */
    public function remove(int $offset){

        if(!($offset >= 0 && $offset < $this->length)){
            throw new \OutOfBoundsException();
        }

        unset($this->container[$offset]);

        $this->length--;
        $this->rebaseIndexes();

    }

    /**
     * Removes the first index item.
     */
    public function removeFirst(){
        $value = array_shift($this->container);
        $this->length--;

        //ksort($this->container);
        return $value;
    }

    /**
     * Removes the last index item.
     */
    public function removeLast() {
        $value = array_pop($this->container);
        $this->length--;

        //ksort($this->container);
        return $value;
    }


    public function removeByValue($value){ }

    /**
     *
     * Iterates over the container, returns filtered array.
     *
     * @param  callable $callback [description]
     * @return array              [description]
     */
    public function filter(callable $callback, bool $applyChanges = false) : array {

        $filteredArray = array_filter($this->container, $callback);

        if($applyChanges){
            $this->container = $filteredArray;
        }

        return $filteredArray;

    }

    /**
     * Iterates over the array
     * @return array [description]
     */
    public function map(callable $callback, bool $applyChanges = false) : array {

        $result = array_map($callback, $this->container);

        if($applyChanges){
            $this->container = $result;
        }

        return $result;
    }

    public function search($needle) {
        return array_search($needle, $this->container);
    }

    public function offsetExists ( $offset ) : bool {
        return ($offset >= 0 && $offset < $this->length);
    }

    public function offsetGet ( $offset ) {
        return $this->get($offset);
    }

    public function offsetSet ( $offset , $value ) {
        throw new \NotImplementedException();
    }

    public function offsetUnset ( $offset ) {
        $this->remove($offset);
    }

    /**
    * Allows this to be iteratable.
    * @return ArrayIterator
    */
    public function getIterator() : \ArrayIterator {
        return new \ArrayIterator($this->container);
    }

}

?>
